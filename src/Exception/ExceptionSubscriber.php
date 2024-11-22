<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;

class ExceptionSubscriber
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = null;

        // Xử lý lỗi AppException
        if ($exception instanceof AppException) {
            $response = $this->createErrorResponse(
                $exception->getErrorCode(),
                $exception->getMessage(),
                $exception->getHttpStatus()
            );
        } 
        // Xử lý lỗi AccessDenied
        elseif ($exception instanceof AccessDeniedHttpException) {
            $response = $this->createErrorResponse(
                ErrorCodeProvider::getCode('E2002'),
                ErrorCodeProvider::getMessage('E2002'),
                ErrorCodeProvider::getHttpStatus('E2002')
            );
        } 
        // Xử lý lỗi Route không tìm thấy
        elseif ($exception instanceof NotFoundHttpException) {
            $routeCollection = $this->router->getRouteCollection();
            $allowedRoutes = array_keys($routeCollection->all());
            $currentRoute = $event->getRequest()->attributes->get('_route');

            if ($currentRoute && !in_array($currentRoute, $allowedRoutes, true)) {
                $response = $this->createErrorResponse(
                    ErrorCodeProvider::getCode('E0001'),
                    'Route không hợp lệ',
                    403
                );
            } else {
                $response = $this->createErrorResponse(
                    ErrorCodeProvider::getCode('E0001'),
                    'Route không được tìm thấy',
                    404
                );
            }
        } 
        // Xử lý các lỗi khác
        else {
            $response = $this->createErrorResponse(
                ErrorCodeProvider::getCode('E0000'),
                $exception->getMessage() ?: ErrorCodeProvider::getMessage('E0000'),
                ErrorCodeProvider::getHttpStatus('E0000')
            );
        }

        $event->setResponse($response);
    }

    private function createErrorResponse(int $errorCode, string $message, int $httpStatus): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'error_code' => $errorCode,
            'message' => $message,
        ], $httpStatus);
    }
}

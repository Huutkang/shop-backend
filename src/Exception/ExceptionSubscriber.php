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

        // // 1. Xử lý lỗi AppException (lỗi tuỳ chỉnh của ứng dụng)
        // if ($exception instanceof AppException) {
        //     $response = $this->createErrorResponse(
        //         $exception->getErrorCode(),
        //         $exception->getMessage(),
        //         $exception->getHttpStatus()
        //     );
        // }
        // // 2. Xử lý lỗi quyền truy cập
        // elseif ($exception instanceof AccessDeniedHttpException) {
        //     $response = $this->createErrorResponse(
        //         ErrorCodeProvider::getCode('E10101'), // 'Người dùng không có quyền thực hiện hành động này'
        //         ErrorCodeProvider::getMessage('E10101'),
        //         ErrorCodeProvider::getHttpStatus('E10101') // 403
        //     );
        // } 
        // // 3. Xử lý lỗi Route không tìm thấy
        // elseif ($exception instanceof NotFoundHttpException) {
        //     $routeCollection = $this->router->getRouteCollection();
        //     $allowedRoutes = array_keys($routeCollection->all());
        //     $currentRoute = $event->getRequest()->attributes->get('_route');

        //     if ($currentRoute && !in_array($currentRoute, $allowedRoutes, true)) {
        //         $response = $this->createErrorResponse(
        //             ErrorCodeProvider::getCode('E10002'), // 'Route không hợp lệ'
        //             ErrorCodeProvider::getMessage('E10002'),
        //             ErrorCodeProvider::getHttpStatus('E10002') // 403
        //         );
        //     } else {
        //         $response = $this->createErrorResponse(
        //             ErrorCodeProvider::getCode('E10003'), // 'Route không được tìm thấy'
        //             ErrorCodeProvider::getMessage('E10003'),
        //             ErrorCodeProvider::getHttpStatus('E10003') // 404
        //         );
        //     }
        // } 
        // // 4. Xử lý các lỗi hệ thống khác (fallback)
        // else {
        //     $response = $this->createErrorResponse(
        //         ErrorCodeProvider::getCode('E10000'), // 'Lỗi không xác định trong hệ thống'
        //         $exception->getMessage() ?: ErrorCodeProvider::getMessage('E10000'),
        //         ErrorCodeProvider::getHttpStatus('E10000') // 500
        //     );
        // }

        // $event->setResponse($response);
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

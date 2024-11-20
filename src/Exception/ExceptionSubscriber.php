<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExceptionSubscriber
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = null;

        if ($exception instanceof AppException) {
            $response = $this->createErrorResponse(
                $exception->getErrorCode(),
                $exception->getMessage(),
                $exception->getHttpStatus()
            );
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $response = $this->createErrorResponse(
                ErrorCodeProvider::getCode('E2002'),
                ErrorCodeProvider::getMessage('E2002'),
                ErrorCodeProvider::getHttpStatus('E2002')
            );
        } else {
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

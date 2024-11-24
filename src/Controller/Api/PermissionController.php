<?php

namespace App\Controller;

use App\Service\AuthorizationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PermissionController
{
    private $authorizationService;

    public function __construct(AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    public function checkPermission(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        $permissionName = $request->get('permission');
        $targetId = $request->get('target_id');

        $hasPermission = $this->authorizationService->checkPermission($userId, $permissionName, $targetId);

        return new JsonResponse(['has_permission' => $hasPermission], 200);
    }
}

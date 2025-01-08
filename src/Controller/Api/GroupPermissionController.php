<?php

namespace App\Controller\Api;

use App\Service\GroupPermissionService;
use App\Service\AuthorizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;

#[Route('/api/group-permissions', name: 'group_permissions_')]
class GroupPermissionController extends AbstractController
{
    private GroupPermissionService $service;
    private AuthorizationService $authorizationService;

    public function __construct(GroupPermissionService $service, AuthorizationService $authorizationService)
    {
        $this->service = $service;
        $this->authorizationService = $authorizationService;
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        $a = $this->authorizationService->checkPermission($userCurrent, "create_permission");
        if (!$a) {
            throw new AppException('E2021');
        }
        $data = json_decode($request->getContent(), true);

        try {
            $groupPermission = $this->service->assignPermission($data);
            return $this->json($groupPermission, 201);
        } catch (AppException $e) {
            return $this->json(['error_code' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['error_message' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        $a = $this->authorizationService->checkPermission($userCurrent, "edit_permission");
        if (!$a) {
            throw new AppException('E2021');
        }
        $data = json_decode($request->getContent(), true);

        try {
            $groupPermission = $this->service->updatePermission($id, $data);
            return $this->json($groupPermission);
        } catch (AppException $e) {
            return $this->json(['error_code' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['error_message' => $e->getMessage()], 500);
        }
    }

    #[Route('/{groupId}/{permissionName}', methods: ['GET'])]
    public function hasPermission(int $groupId, string $permissionName, Request $request): JsonResponse
    {
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        $a = $this->authorizationService->checkPermission($userCurrent, "view_permissions");
        if (!$a) {
            throw new AppException('E2021');
        }
        $targetId = $request->query->get('target_id');

        try {
            
            $hasPermission = $this->service->hasPermission($groupId, $permissionName, $targetId);
            return $this->json(['has_permission' => $hasPermission]);
        } catch (AppException $e) {
            return $this->json(['error_code' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['error_message' => $e->getMessage()], 500);
        }
    }
}

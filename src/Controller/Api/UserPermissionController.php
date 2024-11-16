<?php

namespace App\Controller\Api;

use App\Service\UserPermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user-permissions', name: 'user_permission_')]
class UserPermissionController extends AbstractController
{
    private UserPermissionService $userPermissionService;

    public function __construct(UserPermissionService $userPermissionService)
    {
        $this->userPermissionService = $userPermissionService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $permissions = $this->userPermissionService->getAllUserPermissions();
        return $this->json($permissions);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $permission = $this->userPermissionService->getUserPermissionById($id);
        if (!$permission) {
            return $this->json(['message' => 'UserPermission not found'], 404);
        }

        return $this->json($permission);
    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $permission = $this->userPermissionService->addUserPermission($data);
            return $this->json($permission, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $permission = $this->userPermissionService->updateUserPermission($id, $data);
            return $this->json($permission);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->userPermissionService->deleteUserPermission($id);
            return $this->json(['message' => 'UserPermission deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

<?php

namespace App\Controller\Api;

use App\Service\GroupPermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/group-permissions', name: 'group_permission_')]
class GroupPermissionController extends AbstractController
{
    private GroupPermissionService $groupPermissionService;

    public function __construct(GroupPermissionService $groupPermissionService)
    {
        $this->groupPermissionService = $groupPermissionService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $permissions = $this->groupPermissionService->getAllGroupPermissions();
        return $this->json($permissions);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $permission = $this->groupPermissionService->getGroupPermissionById($id);
        if (!$permission) {
            return $this->json(['message' => 'GroupPermission not found'], 404);
        }

        return $this->json($permission);
    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $permission = $this->groupPermissionService->addGroupPermission($data);
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
            $permission = $this->groupPermissionService->updateGroupPermission($id, $data);
            return $this->json($permission);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->groupPermissionService->deleteGroupPermission($id);
            return $this->json(['message' => 'GroupPermission deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

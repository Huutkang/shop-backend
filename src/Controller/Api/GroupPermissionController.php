<?php

namespace App\Controller\Api;

use App\Service\GroupPermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;
use App\Dto\GroupPermissionDto;




#[Route('/api/group-permissions', name: 'group_permissions_')]
class GroupPermissionController extends AbstractController
{
    private GroupPermissionService $service;

    public function __construct(GroupPermissionService $service)
    {
        $this->service = $service;
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $permissions = $this->service->getAllPermissions();
            return $this->json($permissions);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        try {
            $permission = $this->service->getPermissionById($id);
            if (!$permission) {
                return $this->json(['message' => 'Permission not found'], 404);
            }
            return $this->json($permission);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $permission = $this->service->assignPermission($data);
            return $this->json(new GroupPermissionDto($permission), 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $permission = $this->service->updatePermission($id, $data);
            return $this->json($permission);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->service->deletePermission($id);
            return $this->json(['message' => 'Permission deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

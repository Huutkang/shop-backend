<?php

namespace App\Controller\Api;

use App\Service\UserPermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;

#[Route('/api/user-permissions')]
class UserPermissionController extends AbstractController
{
    private UserPermissionService $service;

    public function __construct(UserPermissionService $service)
    {
        $this->service = $service;
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json($this->service->getAllPermissions());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $permission = $this->service->getPermissionById($id);
        return $permission
            ? $this->json($permission)
            : $this->json(['message' => 'Permission not found'], 404);
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $permission = $this->service->assignPermission($data);
            return $this->json($permission, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
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

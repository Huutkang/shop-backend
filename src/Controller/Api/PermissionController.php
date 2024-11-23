<?php

namespace App\Controller\Api;

use App\Service\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;



#[Route('/api/permissions', name: 'permission_')]
class PermissionController extends AbstractController
{
    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $permissions = $this->permissionService->getAllPermissions();
        return $this->json($permissions);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $permission = $this->permissionService->getPermissionById($id);
        if (!$permission) {
            return $this->json(['message' => 'Permission not found'], 404);
        }

        return $this->json($permission);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $permission = $this->permissionService->createPermission($data);
            $em->persist($permission);
            $em->flush();

            return $this->json($permission, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $permission = $this->permissionService->updatePermission($id, $data);
            $em->flush();

            return $this->json($permission);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $this->permissionService->deletePermission($id);
            $em->flush();

            return $this->json(['message' => 'Permission deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

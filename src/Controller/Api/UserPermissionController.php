<?php

namespace App\Controller\Api;

use App\Service\UserPermissionService;
use App\Service\UserService;
use App\Service\AuthorizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;

#[Route('/api/user-permissions')]
class UserPermissionController extends AbstractController
{
    private UserPermissionService $service;
    private UserService $userService;
    private AuthorizationService $authorizationService;

    public function __construct(UserPermissionService $service, UserService $userService, AuthorizationService $authorizationService)
    {
        $this->service = $service;
        $this->userService = $userService;
        $this->authorizationService = $authorizationService;
    }

    #[Route('', methods: ['POST'])]
    public function assignPermission(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userPermission = $this->service->assignPermission($data);
            return $this->json($userPermission, 201);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    #[Route('/{userId}', methods: ['GET'])]
    public function getPermissionsByUser(int $userId): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($userId);
            $userPermissions = $this->service->getPermissionsByUser($user);
            return $this->json($userPermissions);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function updatePermission(Request $request, int $id): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userPermission = $this->service->updatePermission($id, $data);
            return $this->json($userPermission);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    #[Route('/check', methods: ['POST'])]
    public function hasPermission(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userId = $data['user_id'] ?? null;
            $permissionName = $data['permission_name'] ?? null;
            $targetId = $data['target_id'] ?? null;

            if (!$userId || !$permissionName) {
                return $this->json(['message' => 'Invalid input.'], 400);
            }

            $hasPermission = $this->service->hasPermission($userId, $permissionName, $targetId);
            return $this->json(['has_permission' => $hasPermission]);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}

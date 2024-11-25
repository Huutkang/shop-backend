<?php

namespace App\Controller\Api;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;
use App\Dto\UserDto;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        $userDtos = array_map(fn($user) => new UserDto($user), $users);
        return $this->json($userDtos);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json(new UserDto($user));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->userService->createUser($data);
            return $this->json(new UserDto($user), 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->userService->updateUser($id, $data);
            return $this->json(new UserDto($user));
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            return $this->json(['message' => 'User deleted']);
        } catch (\Exception $e) {
            throw new AppException('E1007');
        }
    }

    // #[Route('/checkpassword', name: 'check', methods: ['POST'])]
    // public function checkpassword(Request $request): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     try {
    //         $isValid = $this->userService->checkPassword($data['username'], $data['password']);
    //         return $this->json(['isValid' => $isValid]);
    //     } catch (\Exception $e) {
    //         throw new AppException('E1005');
    //     }
    // }
}

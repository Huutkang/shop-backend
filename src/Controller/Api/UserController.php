<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;


#[Route('/api/users', name: 'user_')]
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
        throw new AppException('E10101', 'Bạn không có quyền xoá sản phẩm này');
        $users = $this->userService->getAllUsers();
        return $this->json($users);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        return $this->json($user);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->userService->createUser($data);
            $em->persist($user);
            $em->flush();

            return $this->json($user, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->userService->updateUser($id, $data);
            $em->flush();

            return $this->json($user);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            $em->flush();

            return $this->json(['message' => 'User deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

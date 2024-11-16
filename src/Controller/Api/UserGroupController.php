<?php

namespace App\Controller\Api;

use App\Service\UserGroupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user-groups', name: 'user_group_')]
class UserGroupController extends AbstractController
{
    private UserGroupService $userGroupService;

    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $userGroups = $this->userGroupService->getAllUserGroups();
        return $this->json($userGroups);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $userGroup = $this->userGroupService->getUserGroupById($id);
        if (!$userGroup) {
            return $this->json(['message' => 'UserGroup not found'], 404);
        }

        return $this->json($userGroup);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $userGroup = $this->userGroupService->createUserGroup($data);
            $em->persist($userGroup);
            $em->flush();

            return $this->json($userGroup, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $userGroup = $this->userGroupService->updateUserGroup($id, $data);
            $em->flush();

            return $this->json($userGroup);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $this->userGroupService->deleteUserGroup($id);
            $em->flush();

            return $this->json(['message' => 'UserGroup deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

<?php

namespace App\Controller\Api;

use App\Service\GroupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;
use App\Dto\GroupDto;


#[Route('/api/user-groups', name: 'user_group_')]
class GroupController extends AbstractController
{
    private GroupService $userGroupService;

    public function __construct(GroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $userGroups = $this->userGroupService->getAllGroups();
        return $this->json($userGroups);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $userGroup = $this->userGroupService->getGroupById($id);
        if (!$userGroup) {
            return $this->json(['message' => 'Group not found'], 404);
        }

        return $this->json($userGroup);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $userGroup = $this->userGroupService->createGroup($data);
            $em->persist($userGroup);
            $em->flush();

            return $this->json(new GroupDto($userGroup), 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $userGroup = $this->userGroupService->updateGroup($id, $data);
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
            $this->userGroupService->deleteGroup($id);
            $em->flush();

            return $this->json(['message' => 'Group deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

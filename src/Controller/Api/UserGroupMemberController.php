<?php

namespace App\Controller\Api;

use App\Service\UserGroupMemberService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user-group-members', name: 'user_group_member_')]
class UserGroupMemberController extends AbstractController
{
    private UserGroupMemberService $userGroupMemberService;

    public function __construct(UserGroupMemberService $userGroupMemberService)
    {
        $this->userGroupMemberService = $userGroupMemberService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $members = $this->userGroupMemberService->getAllMembers();
        return $this->json($members);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $member = $this->userGroupMemberService->getMemberById($id);
        if (!$member) {
            return $this->json(['message' => 'UserGroupMember not found'], 404);
        }

        return $this->json($member);
    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $member = $this->userGroupMemberService->addMember($data);
            return $this->json($member, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $member = $this->userGroupMemberService->updateMember($id, $data);
            return $this->json($member);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->userGroupMemberService->deleteMember($id);
            return $this->json(['message' => 'UserGroupMember deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}

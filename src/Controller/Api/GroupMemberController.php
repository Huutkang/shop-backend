<?php

namespace App\Controller;

use App\Service\GroupMemberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupMemberController extends AbstractController
{
    private GroupMemberService $groupMemberService;

    public function __construct(GroupMemberService $groupMemberService)
    {
        $this->groupMemberService = $groupMemberService;
    }

    #[Route('/group-member/add', name: 'add_group_member', methods: ['POST'])]
    public function addUserToGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'], $data['group_id'])) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }

        try {
            $groupMember = $this->groupMemberService->addUserToGroup($data);

            return $this->json(['message' => 'User added to group successfully', 'group_member' => $groupMember], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/group-member/remove', name: 'remove_group_member', methods: ['POST'])]
    public function removeUserFromGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'], $data['group_id'])) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }

        try {
            $this->groupMemberService->removeUserFromGroup($data);

            return $this->json(['message' => 'User removed from group successfully'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/group-member/groups', name: 'get_groups_for_user', methods: ['GET'])]
    public function getGroupsForUser(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId');

        if (!$userId) {
            return $this->json(['error' => 'Missing userId parameter'], 400);
        }

        try {
            $groups = $this->groupMemberService->getGroupsForUser(['userId' => $userId]);

            return $this->json(['groups' => $groups], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/group-member/users', name: 'get_users_in_group', methods: ['GET'])]
    public function getUsersInGroup(Request $request): JsonResponse
    {
        $groupId = $request->query->get('group_id');

        if (!$groupId) {
            return $this->json(['error' => 'Missing group_id parameter'], 400);
        }

        try {
            $users = $this->groupMemberService->getUsersInGroup(['group_id' => $groupId]);

            return $this->json(['users' => $users], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/group-member/check', name: 'is_user_in_group', methods: ['GET'])]
    public function isUserInGroup(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId');
        $groupId = $request->query->get('group_id');

        if (!$userId || !$groupId) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }

        try {
            $isInGroup = $this->groupMemberService->isUserInGroup(['userId' => $userId, 'group_id' => $groupId]);

            return $this->json(['is_in_group' => $isInGroup], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}

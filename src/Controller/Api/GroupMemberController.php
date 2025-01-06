<?php

namespace App\Controller\Api;

use App\Service\GroupMemberService;
use App\Dto\GroupDto;
use App\Dto\UserDto;
use App\Dto\GroupMemberDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;


#[Route('/api/group-member', name: 'group-member_')]
class GroupMemberController extends AbstractController
{
    private GroupMemberService $groupMemberService;

    public function __construct(GroupMemberService $groupMemberService)
    {
        $this->groupMemberService = $groupMemberService;
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addUserToGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'], $data['groupId'])) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }
        $groupMember = $this->groupMemberService->addUserToGroup($data);
        return $this->json(['message' => 'User added to group successfully', 'group_member' => new GroupMemberDto($groupMember)], 201);
    }

    #[Route('/remove', name: 'remove', methods: ['POST'])]
    public function removeUserFromGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'], $data['groupId'])) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }

        try {
            $this->groupMemberService->removeUserFromGroup($data);

            return $this->json(['message' => 'User removed from group successfully'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/user/groups', name: 'get_groups_for_user_current', methods: ['GET'])]
    public function getGroupsForUserCurrent(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');
        if (!$user){
            throw new AppException('E2025');
        }
        $groups = $this->groupMemberService->findGroupsForUser($user);
        $groupDtos = array_map(fn($group) => new GroupDto($group), $groups);
        return $this->json($groupDtos, 200);
    }

    #[Route('/user_{id}/groups', name: 'get_groups_for_user', methods: ['GET'])]
    public function getGroupsForUser(int $id): JsonResponse
    {
        $groups = $this->groupMemberService->getGroupsForUser($id);
        $groupDtos = array_map(fn($group) => new GroupDto($group), $groups);
        return $this->json($groupDtos, 200);
    }

    #[Route('/group_{id}/users', name: 'get_users_in_group', methods: ['GET'])]
    public function getUsersInGroup(int $id): JsonResponse
    {
        $users = $this->groupMemberService->getUsersInGroup($id);
        $userDtos = array_map(fn($user) => new UserDto($user), $users);
        return $this->json($userDtos, 200);
    }

    #[Route('/check', name: 'is_user_in_group', methods: ['GET'])]
    public function isUserInGroup(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId');
        $groupId = $request->query->get('groupId');

        if (!$userId || !$groupId) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }

        try {
            $isInGroup = $this->groupMemberService->isUserInGroup(['userId' => $userId, 'groupId' => $groupId]);

            return $this->json(['is_in_group' => $isInGroup], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}

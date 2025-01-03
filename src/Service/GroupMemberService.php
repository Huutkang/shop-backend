<?php

namespace App\Service;

use App\Entity\GroupMember;
use App\Entity\User;
use App\Repository\GroupMemberRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupMemberService
{
    private EntityManagerInterface $entityManager;
    private GroupMemberRepository $groupMemberRepository;
    private UserService $userService;
    private GroupService $groupService;

    public function __construct(
        EntityManagerInterface $entityManager,
        GroupMemberRepository $groupMemberRepository,
        UserService $userService,
        GroupService $groupService
    ) {
        $this->entityManager = $entityManager;
        $this->groupMemberRepository = $groupMemberRepository;
        $this->userService = $userService;
        $this->groupService = $groupService;
    }

    public function addUserToGroup(array $data): GroupMember
    {
        $user = $this->userService->getUserById($data['userId']);
        $group = $this->groupService->getGroupById($data['group_id']);

        $groupMember = new GroupMember();
        $groupMember->setUser($user);
        $groupMember->setGroup($group);

        $this->entityManager->persist($groupMember);
        $this->entityManager->flush();

        return $groupMember;
    }

    public function removeUserFromGroup(array $data): void
    {
        $user = $this->userService->getUserById($data['userId']);
        $group = $this->groupService->getGroupById($data['group_id']);

        $groupMember = $this->groupMemberRepository->findByUserAndGroup($user, $group);

        if ($groupMember) {
            $this->entityManager->remove($groupMember);
            $this->entityManager->flush();
        }
    }

    public function getGroupsForUser(array $data): array
    {
        $user = $this->userService->getUserById($data['userId']);

        return $this->groupMemberRepository->findGroupsByUser($user);
    }

    public function getGroupsByUser(User $user): array{
        return $this->groupMemberRepository->findGroupsByUser($user);
    }

    public function getUsersInGroup(array $data): array
    {
        $group = $this->groupService->getGroupById($data['group_id']);

        return $this->groupMemberRepository->findUsersByGroup($group);
    }

    public function isUserInGroup(array $data): bool
    {
        $user = $this->userService->getUserById($data['userId']);
        $group = $this->groupService->getGroupById($data['group_id']);

        return $this->groupMemberRepository->existsByUserAndGroup($user, $group);
    }
}
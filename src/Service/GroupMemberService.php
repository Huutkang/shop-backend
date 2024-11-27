<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Group;
use App\Entity\GroupMember;
use App\Repository\GroupMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class GroupMemberService
{
    private GroupMemberRepository $groupMemberRepository;
    private EntityManagerInterface $entityManager;
    private UserService $userService;
    private GroupService $groupService;

    public function __construct(
        GroupMemberRepository $groupMemberRepository,
        EntityManagerInterface $entityManager,
        UserService $userService,
        GroupService $groupService
    ) {
        $this->groupMemberRepository = $groupMemberRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->groupService = $groupService;
    }

    // Lấy danh sách các nhóm mà user thuộc về
    public function getGroupsByUser(User $user): array
    {
        $groupMembers = $this->groupMemberRepository->findBy(['user' => $user]);

        $groups = array_map(function (GroupMember $groupMember) {
            return $groupMember->getGroup();
        }, $groupMembers);

        return $groups;
    }

    public function getAllMembers(): array
    {
        return $this->groupMemberRepository->findAll();
    }

    public function getMemberById(array $ids): ?GroupMember
    {
        return $this->groupMemberRepository->findOneBy($ids);
    }

    public function addMember(array $data): GroupMember
    {
        // Lấy đối tượng User từ UserService
        $user = $this->userService->getUserById($data['user_id']);
        if (!$user) {
            throw new AppException('User not found');
        }

        // Lấy đối tượng Group từ GroupService
        $group = $this->groupService->getGroupById($data['group_id']);
        if (!$group) {
            throw new AppException('Group not found');
        }

        $member = new GroupMember();
        $member->setUser($user)
            ->setGroup($group);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    public function updateMember(array $ids, array $data): GroupMember
    {
        $member = $this->getMemberById($ids);

        if (!$member) {
            throw new AppException('GroupMember not found');
        }

        if (isset($data['user_id'])) {
            $user = $this->userService->getUserById($data['user_id']);
            if (!$user) {
                throw new AppException('User not found');
            }
            $member->setUser($user);
        }

        if (isset($data['group_id'])) {
            $group = $this->groupService->getGroupById($data['group_id']);
            if (!$group) {
                throw new AppException('Group not found');
            }
            $member->setGroup($group);
        }

        $this->entityManager->flush();

        return $member;
    }

    public function deleteMember(array $ids): void
    {
        $member = $this->getMemberById($ids);

        if (!$member) {
            throw new AppException('GroupMember not found');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }
}

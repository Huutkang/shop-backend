<?php

namespace App\Service;

use App\Entity\UserGroupMember;
use App\Repository\UserGroupMemberRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserGroupMemberService
{
    private UserGroupMemberRepository $userGroupMemberRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserGroupMemberRepository $userGroupMemberRepository, EntityManagerInterface $entityManager)
    {
        $this->userGroupMemberRepository = $userGroupMemberRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllMembers(): array
    {
        return $this->userGroupMemberRepository->findAll();
    }

    public function getMemberById(int $id): ?UserGroupMember
    {
        return $this->userGroupMemberRepository->find($id);
    }

    public function addMember(array $data): UserGroupMember
    {
        $member = new UserGroupMember();
        $member->setUser($data['user'] ?? throw new \Exception('User is required'))
               ->setGroup($data['group'] ?? throw new \Exception('Group is required'));

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    public function updateMember(int $id, array $data): UserGroupMember
    {
        $member = $this->getMemberById($id);

        if (!$member) {
            throw new \Exception('UserGroupMember not found');
        }

        $member->setUser($data['user'] ?? $member->getUser())
               ->setGroup($data['group'] ?? $member->getGroup());

        $this->entityManager->flush();

        return $member;
    }

    public function deleteMember(int $id): void
    {
        $member = $this->getMemberById($id);

        if (!$member) {
            throw new \Exception('UserGroupMember not found');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }
}

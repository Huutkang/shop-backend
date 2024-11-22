<?php

namespace App\Service;

use App\Entity\GroupMember;
use App\Repository\GroupMemberRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupMemberService
{
    private GroupMemberRepository $groupMemberRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(GroupMemberRepository $groupMemberRepository, EntityManagerInterface $entityManager)
    {
        $this->groupMemberRepository = $groupMemberRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllMembers(): array
    {
        return $this->groupMemberRepository->findAll();
    }

    public function getMemberById(int $id): ?GroupMember
    {
        return $this->groupMemberRepository->find($id);
    }

    public function addMember(array $data): GroupMember
    {
        $member = new GroupMember();
        $member->setUser($data['user'] ?? throw new \Exception('User is required'))
               ->setGroup($data['group'] ?? throw new \Exception('Group is required'));

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    public function updateMember(int $id, array $data): GroupMember
    {
        $member = $this->getMemberById($id);

        if (!$member) {
            throw new \Exception('GroupMember not found');
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
            throw new \Exception('GroupMember not found');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }
}

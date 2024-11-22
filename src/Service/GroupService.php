<?php

namespace App\Service;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{
    private GroupRepository $groupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(GroupRepository $groupRepository, EntityManagerInterface $entityManager)
    {
        $this->groupRepository = $groupRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllGroups(): array
    {
        return $this->groupRepository->findAll();
    }

    public function getGroupById(int $id): ?Group
    {
        return $this->groupRepository->find($id);
    }

    public function createGroup(array $data): Group
    {
        $group = new Group();
        $group->setName($data['name'] ?? throw new \Exception('Name is required'))
            ->setDescription($data['description'] ?? null);

        return $group;
    }

    public function updateGroup(int $id, array $data): Group
    {
        $group = $this->getGroupById($id);

        if (!$group) {
            throw new \Exception('Group not found');
        }

        $group->setName($data['name'] ?? $group->getName())
            ->setDescription($data['description'] ?? $group->getDescription());

        return $group;
    }

    public function deleteGroup(int $id): void
    {
        $group = $this->getGroupById($id);

        if (!$group) {
            throw new \Exception('Group not found');
        }

        $this->entityManager->remove($group);
    }
}

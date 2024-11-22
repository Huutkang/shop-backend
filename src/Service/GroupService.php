<?php

namespace App\Service;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{
    private GroupRepository $GroupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(GroupRepository $GroupRepository, EntityManagerInterface $entityManager)
    {
        $this->GroupRepository = $GroupRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllGroups(): array
    {
        return $this->GroupRepository->findAll();
    }

    public function getGroupById(int $id): ?Group
    {
        return $this->GroupRepository->find($id);
    }

    public function createGroup(array $data): Group
    {
        $Group = new Group();
        $Group->setName($data['name'] ?? throw new \Exception('Name is required'))
            ->setDescription($data['description'] ?? null);

        return $Group;
    }

    public function updateGroup(int $id, array $data): Group
    {
        $Group = $this->getGroupById($id);

        if (!$Group) {
            throw new \Exception('Group not found');
        }

        $Group->setName($data['name'] ?? $Group->getName())
            ->setDescription($data['description'] ?? $Group->getDescription());

        return $Group;
    }

    public function deleteGroup(int $id): void
    {
        $Group = $this->getGroupById($id);

        if (!$Group) {
            throw new \Exception('Group not found');
        }

        $this->entityManager->remove($Group);
    }
}

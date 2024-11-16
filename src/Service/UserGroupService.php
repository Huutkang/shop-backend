<?php

namespace App\Service;

use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserGroupService
{
    private UserGroupRepository $userGroupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserGroupRepository $userGroupRepository, EntityManagerInterface $entityManager)
    {
        $this->userGroupRepository = $userGroupRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllUserGroups(): array
    {
        return $this->userGroupRepository->findAll();
    }

    public function getUserGroupById(int $id): ?UserGroup
    {
        return $this->userGroupRepository->find($id);
    }

    public function createUserGroup(array $data): UserGroup
    {
        $userGroup = new UserGroup();
        $userGroup->setName($data['name'] ?? throw new \Exception('Name is required'))
            ->setDescription($data['description'] ?? null);

        return $userGroup;
    }

    public function updateUserGroup(int $id, array $data): UserGroup
    {
        $userGroup = $this->getUserGroupById($id);

        if (!$userGroup) {
            throw new \Exception('UserGroup not found');
        }

        $userGroup->setName($data['name'] ?? $userGroup->getName())
            ->setDescription($data['description'] ?? $userGroup->getDescription());

        return $userGroup;
    }

    public function deleteUserGroup(int $id): void
    {
        $userGroup = $this->getUserGroupById($id);

        if (!$userGroup) {
            throw new \Exception('UserGroup not found');
        }

        $this->entityManager->remove($userGroup);
    }
}

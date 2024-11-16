<?php

namespace App\Service;

use App\Entity\GroupPermission;
use App\Repository\GroupPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupPermissionService
{
    private GroupPermissionRepository $groupPermissionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(GroupPermissionRepository $groupPermissionRepository, EntityManagerInterface $entityManager)
    {
        $this->groupPermissionRepository = $groupPermissionRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllGroupPermissions(): array
    {
        return $this->groupPermissionRepository->findAll();
    }

    public function getGroupPermissionById(int $id): ?GroupPermission
    {
        return $this->groupPermissionRepository->find($id);
    }

    public function addGroupPermission(array $data): GroupPermission
    {
        $groupPermission = new GroupPermission();
        $groupPermission->setGroup($data['group'] ?? throw new \Exception('Group is required'))
                        ->setPermission($data['permission'] ?? throw new \Exception('Permission is required'));

        $this->entityManager->persist($groupPermission);
        $this->entityManager->flush();

        return $groupPermission;
    }

    public function updateGroupPermission(int $id, array $data): GroupPermission
    {
        $groupPermission = $this->getGroupPermissionById($id);

        if (!$groupPermission) {
            throw new \Exception('GroupPermission not found');
        }

        $groupPermission->setGroup($data['group'] ?? $groupPermission->getGroup())
                        ->setPermission($data['permission'] ?? $groupPermission->getPermission());

        $this->entityManager->flush();

        return $groupPermission;
    }

    public function deleteGroupPermission(int $id): void
    {
        $groupPermission = $this->getGroupPermissionById($id);

        if (!$groupPermission) {
            throw new \Exception('GroupPermission not found');
        }

        $this->entityManager->remove($groupPermission);
        $this->entityManager->flush();
    }
}

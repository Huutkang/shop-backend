<?php

namespace App\Service;

use App\Entity\GroupPermission;
use App\Repository\GroupPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupPermissionService
{
    private GroupPermissionRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(GroupPermissionRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function assignPermission(array $data): GroupPermission
    {
        $groupPermission = new GroupPermission();
        $groupPermission->setGroup($data['group'] ?? throw new \Exception('Group is required'))
                        ->setPermissionName($data['permission_name'] ?? throw new \Exception('Permission name is required'))
                        ->setIsActive($data['is_active'] ?? true)
                        ->setIsDenied($data['is_denied'] ?? false)
                        ->setTargetIds($data['target_ids'] ?? []);

        $this->entityManager->persist($groupPermission);
        $this->entityManager->flush();

        return $groupPermission;
    }

    public function updatePermission(int $id, array $data): GroupPermission
    {
        $groupPermission = $this->repository->find($id);

        if (!$groupPermission) {
            throw new \Exception('GroupPermission not found');
        }

        $groupPermission->setIsActive($data['is_active'] ?? $groupPermission->getIsActive())
                        ->setIsDenied($data['is_denied'] ?? $groupPermission->getIsDenied())
                        ->setTargetIds($data['target_ids'] ?? $groupPermission->getTargetIds());

        $this->entityManager->flush();

        return $groupPermission;
    }

    public function deletePermission(int $id): void
    {
        $groupPermission = $this->repository->find($id);

        if (!$groupPermission) {
            throw new \Exception('GroupPermission not found');
        }

        $this->entityManager->remove($groupPermission);
        $this->entityManager->flush();
    }

    public function getAllPermissions(): array
    {
        return $this->repository->findAll();
    }

    public function getPermissionById(int $id): ?GroupPermission
    {
        return $this->repository->find($id);
    }
}

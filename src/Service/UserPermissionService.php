<?php

namespace App\Service;

use App\Entity\UserPermission;
use App\Repository\UserPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserPermissionService
{
    private UserPermissionRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPermissionRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function assignPermission(array $data): UserPermission
    {
        $userPermission = new UserPermission();
        $userPermission->setUser($data['user'] ?? throw new \Exception('User is required'))
                       ->setPermissionName($data['permission_name'] ?? throw new \Exception('Permission name is required'))
                       ->setIsActive($data['is_active'] ?? true)
                       ->setIsDenied($data['is_denied'] ?? false)
                       ->setTargetIds($data['target_ids'] ?? []);

        $this->entityManager->persist($userPermission);
        $this->entityManager->flush();

        return $userPermission;
    }

    public function updatePermission(int $id, array $data): UserPermission
    {
        $userPermission = $this->repository->find($id);

        if (!$userPermission) {
            throw new \Exception('UserPermission not found');
        }

        $userPermission->setIsActive($data['is_active'] ?? $userPermission->getIsActive())
                       ->setIsDenied($data['is_denied'] ?? $userPermission->getIsDenied())
                       ->setTargetIds($data['target_ids'] ?? $userPermission->getTargetIds());

        $this->entityManager->flush();

        return $userPermission;
    }

    public function deletePermission(int $id): void
    {
        $userPermission = $this->repository->find($id);

        if (!$userPermission) {
            throw new \Exception('UserPermission not found');
        }

        $this->entityManager->remove($userPermission);
        $this->entityManager->flush();
    }

    public function getAllPermissions(): array
    {
        return $this->repository->findAll();
    }

    public function getPermissionById(int $id): ?UserPermission
    {
        return $this->repository->find($id);
    }
}

<?php

namespace App\Service;

use App\Entity\UserPermission;
use App\Repository\UserPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserPermissionService
{
    private UserPermissionRepository $userPermissionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPermissionRepository $userPermissionRepository, EntityManagerInterface $entityManager)
    {
        $this->userPermissionRepository = $userPermissionRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllUserPermissions(): array
    {
        return $this->userPermissionRepository->findAll();
    }

    public function getUserPermissionById(int $id): ?UserPermission
    {
        return $this->userPermissionRepository->find($id);
    }

    public function addUserPermission(array $data): UserPermission
    {
        $userPermission = new UserPermission();
        $userPermission->setUser($data['user'] ?? throw new \Exception('User is required'))
                       ->setPermission($data['permission'] ?? throw new \Exception('Permission is required'));

        $this->entityManager->persist($userPermission);
        $this->entityManager->flush();

        return $userPermission;
    }

    public function updateUserPermission(int $id, array $data): UserPermission
    {
        $userPermission = $this->getUserPermissionById($id);

        if (!$userPermission) {
            throw new \Exception('UserPermission not found');
        }

        $userPermission->setUser($data['user'] ?? $userPermission->getUser())
                       ->setPermission($data['permission'] ?? $userPermission->getPermission());

        $this->entityManager->flush();

        return $userPermission;
    }

    public function deleteUserPermission(int $id): void
    {
        $userPermission = $this->getUserPermissionById($id);

        if (!$userPermission) {
            throw new \Exception('UserPermission not found');
        }

        $this->entityManager->remove($userPermission);
        $this->entityManager->flush();
    }
}

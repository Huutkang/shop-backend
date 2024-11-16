<?php

namespace App\Service;

use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;

class PermissionService
{
    private PermissionRepository $permissionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(PermissionRepository $permissionRepository, EntityManagerInterface $entityManager)
    {
        $this->permissionRepository = $permissionRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllPermissions(): array
    {
        return $this->permissionRepository->findAll();
    }

    public function getPermissionById(int $id): ?Permission
    {
        return $this->permissionRepository->find($id);
    }

    public function createPermission(array $data): Permission
    {
        $permission = new Permission();
        $permission->setName($data['name'] ?? throw new \Exception('Name is required'))
                   ->setDescription($data['description'] ?? null);

        return $permission;
    }

    public function updatePermission(int $id, array $data): Permission
    {
        $permission = $this->getPermissionById($id);

        if (!$permission) {
            throw new \Exception('Permission not found');
        }

        $permission->setName($data['name'] ?? $permission->getName())
                   ->setDescription($data['description'] ?? $permission->getDescription());

        return $permission;
    }

    public function deletePermission(int $id): void
    {
        $permission = $this->getPermissionById($id);

        if (!$permission) {
            throw new \Exception('Permission not found');
        }

        $this->entityManager->remove($permission);
    }
}

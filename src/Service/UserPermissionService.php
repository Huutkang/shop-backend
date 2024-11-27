<?php

namespace App\Service;

use App\Entity\UserPermission;
use App\Repository\UserPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;


class UserPermissionService
{
    private UserPermissionRepository $repository;
    private EntityManagerInterface $entityManager;
    private UserService $userService;
    private PermissionService $permissionService;

    public function __construct(UserPermissionRepository $repository, EntityManagerInterface $entityManager, UserService $userService, PermissionService $permissionService)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->permissionService = $permissionService;
    }

    public function assignPermission(array $data): UserPermission
    {
        // Lấy đối tượng User từ UserService
        $user = $this->userService->getUserById($data['user_id']);
        if (!$user) {
            throw new AppException('E1004'); 
        }

        // Lấy đối tượng Permission từ PermissionService
        $permission = $this->permissionService->getPermissionById($data['permission_id']);
        if (!$permission) {
            throw new AppException('E2024');
        }
        $userPermission = new UserPermission();
        $userPermission->setUser($user)
                       ->setPermission($permission)
                       ->setIsActive($data['is_active'] ?? true)
                       ->setIsDenied($data['is_denied'] ?? false)
                       ->setTargetId($data['target_ids'] ?? null);

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
                       ->setTargetId($data['target_ids'] ?? $userPermission->getTargetIds());

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

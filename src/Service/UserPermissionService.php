<?php

namespace App\Service;

use App\Entity\UserPermission;
use App\Entity\User;
use App\Entity\Permission;
use App\Repository\UserPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class UserPermissionService
{
    private UserPermissionRepository $repository;
    private EntityManagerInterface $entityManager;
    private UserService $userService;
    private PermissionService $permissionService;

    public function __construct(
        UserPermissionRepository $repository,
        EntityManagerInterface $entityManager,
        UserService $userService,
        PermissionService $permissionService
    ) {
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

    public function setPermission(User $user, array $permissions): array
    {
        $userPermissions = [];

        foreach ($permissions as $permission) {
            if (!$permission instanceof Permission) {
                throw new \InvalidArgumentException('Each item in permissions array must be an instance of Permission.');
            }

            $userPermission = new UserPermission();
            $userPermission->setUser($user)
                ->setPermission($permission)
                ->setIsActive(true) // Giá trị mặc định
                ->setIsDenied(false) // Giá trị mặc định
                ->setTargetId(null); // Giá trị mặc định

            $this->entityManager->persist($userPermission);
            $userPermissions[] = $userPermission;
        }

        $this->entityManager->flush();

        return $userPermissions;
    }

    public function getPermissionsByUser(User $user): array
{
    // Lấy danh sách các quyền của người dùng
    return $this->repository->findBy(['user' => $user]);
}


    public function updatePermission(int $id, array $data): UserPermission
    {
        $userPermission = $this->repository->find($id);

        if (!$userPermission) {
            throw new AppException('E2022');
        }

        // Lấy đối tượng User từ UserService nếu user_id được cung cấp
        if (isset($data['user_id'])) {
            $user = $this->userService->getUserById($data['user_id']);
            if (!$user) {
                throw new AppException('E1004');
            }
            $userPermission->setUser($user);
        }

        // Lấy đối tượng Permission từ PermissionService nếu permission_id được cung cấp
        if (isset($data['permission_id'])) {
            $permission = $this->permissionService->getPermissionById($data['permission_id']);
            if (!$permission) {
                throw new AppException('E2024');
            }
            $userPermission->setPermission($permission);
        }

        $userPermission->setIsActive($data['is_active'] ?? $userPermission->isActive())
            ->setIsDenied($data['is_denied'] ?? $userPermission->isDenied())
            ->setTargetId($data['target_ids'] ?? $userPermission->getTargetId());

        $this->entityManager->flush();

        return $userPermission;
    }

    public function hasPermission(User $user, string $permissionName, ?int $targetId = null): bool
    {
        // Lấy tất cả bản ghi của user có permission trùng khớp
        $userPermissions = $this->repository->findUserPermission($user->getId(), $permissionName);

        foreach ($userPermissions as $permission) {
            // Nếu có bản ghi targetId = null, trả về true
            if ($permission->getTargetId() === null) {
                return true;
            }
            // Nếu có targetId trùng khớp, trả về true
            if ($permission->getTargetId() === $targetId) {
                return true;
            }
        }

        // Không tìm thấy bất kỳ bản ghi nào phù hợp
        return false;
    }
}

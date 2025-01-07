<?php

namespace App\Service;

use App\Entity\GroupPermission;
use App\Entity\Group;
use App\Repository\GroupPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class GroupPermissionService
{
    private GroupPermissionRepository $repository;
    private EntityManagerInterface $entityManager;
    private GroupService $groupService;
    private PermissionService $permissionService;

    public function __construct(
        GroupPermissionRepository $repository,
        EntityManagerInterface $entityManager,
        GroupService $groupService,
        PermissionService $permissionService
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->groupService = $groupService;
        $this->permissionService = $permissionService;
    }

    public function assignPermission(array $data): GroupPermission
    {
        // Lấy đối tượng Group từ GroupService
        $group = $this->groupService->getGroupById($data['group_id']);
        if (!$group) {
            throw new AppException('E2001');
        }

        // Lấy đối tượng Permission từ PermissionService
        $permission = $this->permissionService->getPermissionById($data['permission_id']);
        if (!$permission) {
            throw new AppException('E2024');
        }

        $groupPermission = new GroupPermission();
        $groupPermission->setGroup($group)
            ->setPermission($permission)
            ->setIsActive($data['is_active'] ?? true)
            ->setIsDenied($data['is_denied'] ?? false)
            ->setTargetId($data['target_ids'] ?? null);

        $this->entityManager->persist($groupPermission);
        $this->entityManager->flush();

        return $groupPermission;
    }

    public function updatePermission(int $id, array $data): GroupPermission
    {
        $groupPermission = $this->repository->find($id);

        if (!$groupPermission) {
            throw new AppException('E2022');
        }

        // Lấy đối tượng Group từ GroupService nếu group_id được cung cấp
        if (isset($data['group_id'])) {
            $group = $this->groupService->getGroupById($data['group_id']);
            if (!$group) {
                throw new AppException('E2001');
            }
            $groupPermission->setGroup($group);
        }

        // Lấy đối tượng Permission từ PermissionService nếu permission_id được cung cấp
        if (isset($data['permission_id'])) {
            $permission = $this->permissionService->getPermissionById($data['permission_id']);
            if (!$permission) {
                throw new AppException('E2024');
            }
            $groupPermission->setPermission($permission);
        }

        $groupPermission->setIsActive($data['is_active'] ?? $groupPermission->isActive())
            ->setIsDenied($data['is_denied'] ?? $groupPermission->isDenied())
            ->setTargetId($data['target_ids'] ?? $groupPermission->getTargetId());

        $this->entityManager->flush();

        return $groupPermission;
    }

    public function hasPermission(int $groupId, string $permissionName, ?int $targetId = null): bool
    {
        // Lấy tất cả bản ghi của group có permission trùng khớp
        $groupPermissions = $this->repository->findGroupPermission($groupId, $permissionName);

        foreach ($groupPermissions as $permission) {
            // Nếu có bản ghi targetId = null, trả về true
            if ($permission->getTargetId() === null) {
                if ($permission->isDenied()){
                    return false;
                }
                return true;
            }
            // Nếu có targetId trùng khớp, trả về true
            if ($permission->getTargetId() === $targetId) {
                if ($permission->isDenied()){
                    return false;
                }
                return true;
            }
        }

        // Không tìm thấy bất kỳ bản ghi nào phù hợp
        return false;
    }
}

<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class UserPermissionRepository extends EntityRepository
{
    public function findByUserAndPermission(int $userId, string $permissionName, ?int $targetId = null)
    {
        $qb = $this->createQueryBuilder('up')
            ->where('up.user = :userId')
            ->andWhere('up.permission.name = :permissionName')
            ->setParameter('userId', $userId)
            ->setParameter('permissionName', $permissionName);

        if ($targetId !== null) {
            $qb->andWhere('up.targetId = :targetId')
               ->setParameter('targetId', $targetId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAllByUserAndPermission(int $userId, string $permissionName, ?int $targetId = null): array
    {
        $qb = $this->createQueryBuilder('up')
            ->where('up.user = :userId')
            ->andWhere('up.permission.name = :permissionName')
            ->setParameter('userId', $userId)
            ->setParameter('permissionName', $permissionName);

        if ($targetId !== null) {
            $qb->andWhere('up.targetId = :targetId')
            ->setParameter('targetId', $targetId);
        }

        return $qb->getQuery()->getResult();
    }


}

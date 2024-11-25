<?php

namespace App\Repository;

use App\Entity\GroupPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupPermission::class);
    }

    public function findAllByGroupAndPermission(int $groupId, string $permissionName, ?int $targetId = null): array
    {
        $qb = $this->createQueryBuilder('gp')
            ->where('gp.group = :groupId')
            ->andWhere('gp.permission.name = :permissionName')
            ->setParameter('groupId', $groupId)
            ->setParameter('permissionName', $permissionName);

        if ($targetId !== null) {
            $qb->andWhere('gp.targetId = :targetId')
            ->setParameter('targetId', $targetId);
        }

        return $qb->getQuery()->getResult();
    }

}

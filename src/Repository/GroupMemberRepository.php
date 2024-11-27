<?php

namespace App\Repository;

use App\Entity\GroupMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMember::class);
    }

    public function findGroupsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('gm')
            ->select('g') // Lấy dữ liệu nhóm
            ->join('gm.group', 'g') // Liên kết bảng Group
            ->where('gm.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\GroupMember;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMember::class);
    }

    public function findByUserAndGroup(User $user, Group $group): ?GroupMember
    {
        return $this->findOneBy(['user' => $user, 'group' => $group]);
    }

    public function findGroupsByUser(User $user): array
    {
        return $this->createQueryBuilder('gm')
            ->select('g')
            ->innerJoin('gm.group', 'g')
            ->where('gm.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findUsersByGroup(Group $group): array
    {
        return $this->createQueryBuilder('gm')
            ->select('u')
            ->innerJoin('gm.user', 'u')
            ->where('gm.group = :group')
            ->setParameter('group', $group)
            ->getQuery()
            ->getResult();
    }

    public function existsByUserAndGroup(User $user, Group $group): bool
    {
        return (bool) $this->createQueryBuilder('gm')
            ->select('1')
            ->where('gm.user = :user')
            ->andWhere('gm.group = :group')
            ->setParameter('user', $user)
            ->setParameter('group', $group)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

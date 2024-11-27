<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\GroupMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMember::class);
    }

    // Tìm tất cả GroupMember liên quan đến User
    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }
}

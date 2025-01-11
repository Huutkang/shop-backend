<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Tìm kiếm các đơn hàng theo User.
     *
     * @param User $user Đối tượng User để tìm kiếm
     * @return Order[] Danh sách các đơn hàng của User
     */
    public function findOrdersByUser(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC') // Sắp xếp theo thời gian tạo (tùy chỉnh)
            ->getQuery()
            ->getResult();
    }
}

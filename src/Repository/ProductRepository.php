<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Lấy danh sách sản phẩm theo ID danh mục
     */
    public function findByCategoryId(int $categoryId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getResult();
    }
}



// <?php

// namespace App\Repository;

// use App\Entity\Product;
// use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// use Doctrine\Persistence\ManagerRegistry;

// /**
//  * @extends ServiceEntityRepository<Product>
//  */
// class ProductRepository extends ServiceEntityRepository
// {
//     public function __construct(ManagerRegistry $registry)
//     {
//         parent::__construct($registry, Product::class);
//     }

//     public function findByCategory(int $categoryId): array
//     {
//         return $this->createQueryBuilder('p')
//             ->where('p.categoryId = :categoryId')
//             ->setParameter('categoryId', $categoryId)
//             ->orderBy('p.createdAt', 'DESC')
//             ->getQuery()
//             ->getResult();
//     }

//     public function searchByName(string $name): array
//     {
//         return $this->createQueryBuilder('p')
//             ->where('p.name LIKE :name')
//             ->setParameter('name', '%' . $name . '%')
//             ->orderBy('p.createdAt', 'DESC')
//             ->getQuery()
//             ->getResult();
//     }

//     public function countByStatus(string $status): int
//     {
//         return $this->createQueryBuilder('p')
//             ->select('COUNT(p.id)')
//             ->where('p.status = :status')
//             ->setParameter('status', $status)
//             ->getQuery()
//             ->getSingleScalarResult();
//     }
// }

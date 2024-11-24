<?php


namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function findByProduct(int $productId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.product = :productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }

    public function findByReview(int $reviewId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.review = :reviewId')
            ->setParameter('reviewId', $reviewId)
            ->getQuery()
            ->getResult();
    }

    public function remove(File $file, bool $flush = true): void
    {
        $this->_em->remove($file);
        if ($flush) {
            $this->_em->flush();
        }
    }
}

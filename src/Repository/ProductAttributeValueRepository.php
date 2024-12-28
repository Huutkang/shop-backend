<?php

namespace App\Repository;

use App\Entity\ProductAttributeValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductAttributeValue>
 */
class ProductAttributeValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductAttributeValue::class);
    }

    public function findByAttributeId(int $attributeId): array
    {
        return $this->createQueryBuilder('pav')
            ->where('pav.attributeId = :attributeId')
            ->setParameter('attributeId', $attributeId)
            ->orderBy('pav.value', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

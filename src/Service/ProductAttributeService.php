<?php

namespace App\Service;

use App\Entity\ProductAttribute;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class ProductAttributeService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProductAttribute(array $data): ProductAttribute
    {
        $productAttribute = new ProductAttribute();
        $productAttribute->setProductId($data['productId'] ?? throw new AppException('Product ID is required'))
                         ->setName($data['name'] ?? throw new AppException('Attribute name is required'));

        $this->entityManager->persist($productAttribute);
        $this->entityManager->flush();

        return $productAttribute;
    }

    public function updateProductAttribute(ProductAttribute $productAttribute, array $data): ProductAttribute
    {
        if (isset($data['name'])) {
            $productAttribute->setName($data['name']);
        }

        $this->entityManager->flush();

        return $productAttribute;
    }

    public function getAttributesByProductId(int $productId): array
    {
        return $this->entityManager->getRepository(ProductAttribute::class)->findByProductId($productId);
    }

    public function deleteProductAttribute(ProductAttribute $productAttribute): void
    {
        $this->entityManager->remove($productAttribute);
        $this->entityManager->flush();
    }
}

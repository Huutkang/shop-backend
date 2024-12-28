<?php

namespace App\Service;

use App\Entity\ProductAttributeValue;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class ProductAttributeValueService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProductAttributeValue(array $data): ProductAttributeValue
    {
        $productAttributeValue = new ProductAttributeValue();
        $productAttributeValue->setAttributeId($data['attributeId'] ?? throw new AppException('Attribute ID is required'))
                              ->setValue($data['value'] ?? throw new AppException('Value is required'));

        $this->entityManager->persist($productAttributeValue);
        $this->entityManager->flush();

        return $productAttributeValue;
    }

    public function updateProductAttributeValue(ProductAttributeValue $productAttributeValue, array $data): ProductAttributeValue
    {
        if (isset($data['value'])) {
            $productAttributeValue->setValue($data['value']);
        }

        $this->entityManager->flush();

        return $productAttributeValue;
    }

    public function getValuesByAttributeId(int $attributeId): array
    {
        return $this->entityManager->getRepository(ProductAttributeValue::class)->findByAttributeId($attributeId);
    }

    public function deleteProductAttributeValue(ProductAttributeValue $productAttributeValue): void
    {
        $this->entityManager->remove($productAttributeValue);
        $this->entityManager->flush();
    }
}

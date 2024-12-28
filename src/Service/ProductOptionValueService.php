<?php

namespace App\Service;

use App\Entity\ProductOptionValue;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class ProductOptionValueService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProductOptionValue(array $data): ProductOptionValue
    {
        $productOptionValue = new ProductOptionValue();
        $productOptionValue->setProductOption($data['productOption'] ?? throw new AppException('Product Option is required'))
                           ->setProductAttributeValue($data['productAttributeValue'] ?? throw new AppException('Product Attribute Value is required'));

        $this->entityManager->persist($productOptionValue);
        $this->entityManager->flush();

        return $productOptionValue;
    }

    public function updateProductOptionValue(ProductOptionValue $productOptionValue, array $data): ProductOptionValue
    {
        if (isset($data['productOption'])) {
            $productOptionValue->setProductOption($data['productOption']);
        }

        if (isset($data['productAttributeValue'])) {
            $productOptionValue->setProductAttributeValue($data['productAttributeValue']);
        }

        $this->entityManager->flush();

        return $productOptionValue;
    }

    public function getValuesByOptionId(int $optionId): array
    {
        return $this->entityManager->getRepository(ProductOptionValue::class)->findByOptionId($optionId);
    }

    public function getValuesByAttributeValueId(int $attributeValueId): array
    {
        return $this->entityManager->getRepository(ProductOptionValue::class)->findByAttributeValueId($attributeValueId);
    }

    public function deleteProductOptionValue(ProductOptionValue $productOptionValue): void
    {
        $this->entityManager->remove($productOptionValue);
        $this->entityManager->flush();
    }
}

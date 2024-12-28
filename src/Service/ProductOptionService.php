<?php

namespace App\Service;

use App\Entity\ProductOption;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class ProductOptionService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProductOption(array $data): ProductOption
    {
        $productOption = new ProductOption();
        $productOption->setProductId($data['productId'] ?? throw new AppException('Product ID is required'))
                      ->setPrice($data['price'] ?? throw new AppException('Price is required'))
                      ->setStock($data['stock'] ?? throw new AppException('Stock is required'));

        $this->entityManager->persist($productOption);
        $this->entityManager->flush();

        return $productOption;
    }

    public function updateProductOption(ProductOption $productOption, array $data): ProductOption
    {
        if (isset($data['price'])) {
            $productOption->setPrice($data['price']);
        }

        if (isset($data['stock'])) {
            $productOption->setStock($data['stock']);
        }

        $this->entityManager->flush();

        return $productOption;
    }

    public function getProductOptionsByProductId(int $productId): array
    {
        return $this->entityManager->getRepository(ProductOption::class)->findByProductId($productId);
    }

    public function updateStock(int $id, int $newStock): void
    {
        $repository = $this->entityManager->getRepository(ProductOption::class);
        $repository->updateStock($id, $newStock);
    }

    public function deleteProductOption(ProductOption $productOption): void
    {
        $this->entityManager->remove($productOption);
        $this->entityManager->flush();
    }
}

<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data): Product
    {
        $product = new Product();
        $product->setName($data['name'] ?? throw new \Exception('Name is required'))
                ->setDescription($data['description'] ?? null)
                ->setPrice($data['price'] ?? throw new \Exception('Price is required'))
                ->setStock($data['stock'] ?? throw new \Exception('Stock is required'))
                ->setUniqueFeatures($data['uniqueFeatures'] ?? null)
                ->setIsFeatured($data['isFeatured'] ?? false)
                ->setCity($data['city'] ?? null)
                ->setDistrict($data['district'] ?? null)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

        return $product;
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->getProductById($id);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $product->setName($data['name'] ?? $product->getName())
                ->setDescription($data['description'] ?? $product->getDescription())
                ->setPrice($data['price'] ?? $product->getPrice())
                ->setStock($data['stock'] ?? $product->getStock())
                ->setUniqueFeatures($data['uniqueFeatures'] ?? $product->getUniqueFeatures())
                ->setIsFeatured($data['isFeatured'] ?? $product->getIsFeatured())
                ->setCity($data['city'] ?? $product->getCity())
                ->setDistrict($data['district'] ?? $product->getDistrict())
                ->setUpdatedAt(new \DateTime());

        return $product;
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->getProductById($id);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $this->entityManager->remove($product);
    }
}

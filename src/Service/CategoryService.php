<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function createCategory(array $data): Category
    {
        $category = new Category();
        $category->setName($data['name'] ?? throw new \Exception('Name is required'))
                 ->setDescription($data['description'] ?? null)
                 ->setParentId($data['parentId'] ?? null);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function updateCategory(int $id, array $data): Category
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            throw new \Exception('Category not found');
        }

        $category->setName($data['name'] ?? $category->getName())
                 ->setDescription($data['description'] ?? $category->getDescription())
                 ->setParentId($data['parentId'] ?? $category->getParentId());

        $this->entityManager->flush();

        return $category;
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            throw new \Exception('Category not found');
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}

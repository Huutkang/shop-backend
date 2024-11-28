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
                 ->setDescription($data['description'] ?? null);

        if (!empty($data['parentId'])) {
            $parent = $this->getCategoryById($data['parentId']);
            if (!$parent) {
                throw new \Exception('Parent category not found');
            }
            $category->setParent($parent);
        }

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
                 ->setDescription($data['description'] ?? $category->getDescription());

        if (array_key_exists('parentId', $data)) {
            $parent = $data['parentId'] ? $this->getCategoryById($data['parentId']) : null;
            if ($data['parentId'] && !$parent) {
                throw new \Exception('Parent category not found');
            }
            $category->setParent($parent);
        }

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

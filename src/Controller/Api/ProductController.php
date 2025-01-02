<?php

namespace App\Controller\Api;

use App\Service\ProductService;
use App\Dto\ProductDto;
use App\Dto\ProductOptionDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;


#[Route('/api/products', name: 'product_')]
class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $products = $this->productService->getAllProductDtos();
        $productDtos = array_map(fn($product) => new ProductDto($product), $products);

        return $this->json($productDtos);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $product = $this->productService->getProductDtoById($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $productDto = new ProductDto($product);
        return $this->json($productDto);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $product = $this->productService->createProduct($data);
            $productDto = new ProductDto($product);
            return $this->json($productDto, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $product = $this->productService->updateProduct($id, $data);
            $productDto = new ProductDto($product);
            return $this->json($productDto);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return $this->json(['message' => 'Product deleted']);
    }

    #[Route('/by-category/{categoryId}', name: 'by_category', methods: ['GET'])]
    public function getProductsByCategoryId(int $categoryId): JsonResponse
    {
        $products = $this->productService->getProductsByCategoryId($categoryId);

        if (empty($products)) {
            return $this->json(['message' => 'No products found for this category'], 404);
        }

        $productDtos = array_map(fn($product) => new ProductDto($product), $products);
        return $this->json($productDtos);
    }

    #[Route('/{id}/attribute', name: 'update_attributes', methods: ['POST', 'PUT'])]
    public function updateAttributes(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Gọi hàm cập nhật hoặc tạo mới từ service
        $this->productService->updateOrCreateProductAttributesAndOptions($id, $data);
        return $this->json(['message' => 'Attributes and options updated successfully'], 200);
    }

    #[Route('/{id}/find-option', name: 'find_option', methods: ['POST'])]
    public function findOption(Request $request, int $id): JsonResponse
    {
        $jsonString = $request->getContent();

        try {
            $product = $this->productService->getProductById($id);
            if (!$product) {
                return $this->json(['message' => 'Product not found'], 404);
            }

            $productOption = $this->productService->findProductOptionByJson($product, $jsonString);

            return $this->json(new ProductOptionDto($productOption));
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
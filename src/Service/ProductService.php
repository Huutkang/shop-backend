<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductOption;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;

class ProductService
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;
    private ProductAttributeService $productAttributeService;
    private ProductAttributeValueService $productAttributeValueService;
    private ProductOptionService $productOptionService;
    private ProductOptionValueService $productOptionValueService;
    

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager,
        ProductAttributeService $productAttributeService,
        ProductAttributeValueService $productAttributeValueService,
        ProductOptionService $productOptionService,
        ProductOptionValueService $productOptionValueService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->productAttributeService = $productAttributeService;
        $this->productAttributeValueService = $productAttributeValueService;
        $this->productOptionService = $productOptionService;
        $this->productOptionValueService = $productOptionValueService;
    }


    public function toDto(Product $product):array {
        $attributes = $this->getProductAttributes($product);
        $priceAndStock = $this->getProductPriceAndStock($product);

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'locationAddress' => $product->getLocationAddress(),
            'categoryId' => $product->getCategory()?->getId(),
            'description' => $product->getDescription(),
            'price' => $priceAndStock['prices'],
            'stock' => $priceAndStock['stock'],
            'attribute' => $attributes,
        ];
    }
    public function getAllProducts(): array
    {
        $products = $this->productRepository->findAll();
        $result = [];

        foreach ($products as $product) {
            $this->toDto($product);
        }

        return $result;
    }
 

    public function getProductById(int $id): ?array
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return null;
        }

        return $this->toDto($product);
    }


    public function getProductAttributes(Product $product): array
    {
        $attributes = $this->productAttributeService->findByProduct($product);
        $result = [];

        foreach ($attributes as $attribute) {
            $values = $this->productAttributeValueService->findByAttribute($attribute);
            $result[$attribute->getName()] = array_map(fn($value) => $value->getValue(), $values);
        }

        return $result;
    }

    private function getProductPriceAndStock(Product $product): array
    {
        $options = $this->productOptionService->findByProduct($product);
        $prices = array_map(fn($option) => $option->getPrice(), $options);
        $totalStock = array_sum(array_map(fn($option) => $option->getStock(), $options));

        return [
            'prices' => count($prices) > 0 ? min($prices) : null, // Giá thấp nhất
            'stock' => $totalStock,
        ];
    }


    public function createProduct(array $data): array
    {
        // Kiểm tra và tạo Product
        $product = new Product();
        $product->setName($data['name'] ?? throw new \Exception('Name is required'))
                ->setLocationAddress($data['locationAddress'] ?? throw new AppException('Location address is required'));

        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }

        if (!empty($data['categoryId'])) {
            $category = $this->categoryRepository->find($data['categoryId']);
            if (!$category) {
                throw new \Exception('Invalid category ID');
            }
            $product->setCategory($category);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Xử lý thuộc tính (attributes)
        if (!empty($data['attribute']) && is_array($data['attribute'])) {
            foreach ($data['attribute'] as $attributeName => $values) {
                if (!is_array($values)) {
                    throw new \Exception('Attribute values must be an array');
                }

                // Tạo ProductAttribute
                $productAttribute = $this->productAttributeService->createProductAttribute($product, $attributeName);

                // Tạo ProductAttributeValue cho mỗi giá trị của thuộc tính
                foreach ($values as $value) {
                    $this->productAttributeValueService->createProductAttributeValue($productAttribute, $value);
                }
            }
        }

        return $this->toDto($product);
    }


    public function updateProduct(int $id, array $data): array
    {
        // Lấy sản phẩm cần cập nhật
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw new \Exception('Product not found');
        }

        // Cập nhật thông tin cơ bản
        if (!empty($data['name'])) {
            $product->setName($data['name']);
        }

        if (!empty($data['locationAddress'])) {
            $product->setLocationAddress($data['locationAddress']);
        }

        if (!empty($data['description'])) {
            $product->setDescription($data['description']);
        }

        if (!empty($data['categoryId'])) {
            $category = $this->categoryRepository->find($data['categoryId']);
            if (!$category) {
                throw new \Exception('Invalid category ID');
            }
            $product->setCategory($category);
        }

        $this->entityManager->persist($product);

        // Xử lý thuộc tính (attributes)
        if (!empty($data['attribute']) && is_array($data['attribute'])) {
            foreach ($data['attribute'] as $attributeName => $values) {
                // Tìm thuộc tính qua service
                $productAttribute = $this->productAttributeService->findByNameAndProduct($attributeName, $product);

                if (!$productAttribute) {
                    // Nếu không tồn tại, tạo mới
                    $productAttribute = $this->productAttributeService->createProductAttribute($product, $attributeName);
                }

                // Lấy tất cả giá trị hiện tại của thuộc tính qua service
                $currentValues = $this->productAttributeValueService->findByAttribute($productAttribute);

                // Xóa giá trị hiện tại nếu không còn trong danh sách mới
                foreach ($currentValues as $currentValue) {
                    if (!in_array($currentValue->getValue(), $values)) {
                        $this->entityManager->remove($currentValue);
                    }
                }

                // Thêm hoặc cập nhật các giá trị mới
                foreach ($values as $value) {
                    $existingValue = $this->productAttributeValueService->findByValueAndAttribute($value, $productAttribute);

                    if (!$existingValue) {
                        // Tạo giá trị mới nếu chưa tồn tại
                        $this->productAttributeValueService->createProductAttributeValue($productAttribute, $value);
                    }
                }
            }
        }

        $this->entityManager->flush();

        return $this->toDto($product);
    }


    public function deleteProduct(int $id): void
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    public function getProductsByCategoryId(int $categoryId): array
    {
        return $this->productRepository->findByCategoryId($categoryId);
    }

    private function findOptionByAttributeValues(Product $product, array $attributeValues): ?ProductOption
    {
        $options = $this->productOptionService->findByProduct($product);
        foreach ($options as $option) {
            $optionValues = $this->productOptionValueService->findByOption($option);
            $optionValueIds = array_map(fn($value) => $value->getProductAttributeValue()->getId(), $optionValues);
            $attributeValueIds = array_map(fn($value) => $value->getId(), $attributeValues);

            if (count(array_diff($optionValueIds, $attributeValueIds)) === 0) {
                return $option;
            }
        }

        return null;
    }

    public function updateOrCreateProductAttributesAndOptions(int $productId, array $jsonData): void
    {
        // Lấy thông tin sản phẩm
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new \Exception('Product not found');
        }

        $attributes = $jsonData['attribute'] ?? [];
        $values = $jsonData['value'] ?? [];

        if (empty($attributes) || empty($values)) {
            throw new \InvalidArgumentException("Invalid input data: 'attribute' and 'value' are required.");
        }

        // Bước 1: Đảm bảo các ProductAttribute tồn tại
        $attributeEntities = [];
        foreach ($attributes as $attributeName) {
            $attribute = $this->productAttributeService->findByNameAndProduct($attributeName, $product);
            if (!$attribute) {
                $attribute = $this->productAttributeService->createProductAttribute($product, $attributeName);
            }
            $attributeEntities[] = $attribute;
        }

        // Bước 2: Xử lý các giá trị ProductAttributeValue và ProductOption
        foreach ($values as $valueSet) {
            $attributeValues = $valueSet[0] ?? [];
            $optionData = $valueSet[1] ?? [];
            $price = $optionData[0] ?? null;
            $stock = $optionData[1] ?? null;

            if (count($attributeValues) !== count($attributes) || $price === null || $stock === null) {
                throw new \InvalidArgumentException("Invalid value set: Mismatch between attributes and values or missing price/stock.");
            }

            // Bước 3: Tạo hoặc cập nhật ProductAttributeValue
            $attributeValueEntities = [];
            foreach ($attributeValues as $index => $value) {
                $attribute = $attributeEntities[$index];
                $attributeValue = $this->productAttributeValueService->findByValueAndAttribute($value, $attribute);
                if (!$attributeValue) {
                    $attributeValue = $this->productAttributeValueService->createProductAttributeValue($attribute, $value);
                }
                $attributeValueEntities[] = $attributeValue;
            }

            // Bước 4: Tạo hoặc cập nhật ProductOption và liên kết với ProductAttributeValue
            $existingOption = $this->findOptionByAttributeValues($product, $attributeValueEntities);
            if (!$existingOption) {
                $productOption = $this->productOptionService->createProductOption($product, $price, $stock);
            } else {
                $this->productOptionService->updateProductOption($existingOption, $price, $stock);
                $productOption = $existingOption;
            }

            // Liên kết ProductOption với ProductAttributeValue
            foreach ($attributeValueEntities as $attributeValueEntity) {
                $existingOptionValue = $this->productOptionValueService->findByValueAndOption($attributeValueEntity, $productOption);
                if (!$existingOptionValue) {
                    $this->productOptionValueService->createProductOptionValue($productOption, $attributeValueEntity);
                }
            }
        }
    }
}


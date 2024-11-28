<?php

namespace App\Dto;

use App\Entity\Product;
use App\Dto\CategoryDto;

class ProductDto
{
    public int $id;
    public string $name;
    public ?string $description;
    public float $price;
    public int $stock;
    public ?string $uniqueFeatures;
    public bool $isFeatured;
    public ?string $city;
    public ?string $district;
    public ?string $categoryHierarchyPath; // Phả hệ của danh mục sản phẩm

    public function __construct(Product $product)
    {
        $this->id = $product->getId();
        $this->name = $product->getName();
        $this->description = $product->getDescription();
        $this->price = $product->getPrice();
        $this->stock = $product->getStock();
        $this->uniqueFeatures = $product->getUniqueFeatures();
        $this->isFeatured = $product->getIsFeatured();
        $this->city = $product->getCity();
        $this->district = $product->getDistrict();

        // Tạo phả hệ danh mục từ Category
        $category = $product->getCategory();
        $this->categoryHierarchyPath = $category ? (new CategoryDto($category))->hierarchyPath : null;
    }
}

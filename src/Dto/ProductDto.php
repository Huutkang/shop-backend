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
    public ?CategoryDto $category;
    public \DateTime $createdAt;
    public \DateTime $updatedAt;

    public function __construct(Product $product)
    {
        $this->id = $product->getId();
        $this->name = $product->getName();
        $this->description = $product->getDescription();
        $this->price = $product->getPrice();
        $this->stock = $product->getStock();
        $this->uniqueFeatures = $product->getUniqueFeatures();
        $this->isFeatured = $product->isFeatured();
        $this->city = $product->getCity();
        $this->district = $product->getDistrict();
        $this->createdAt = $product->getCreatedAt();
        $this->updatedAt = $product->getUpdatedAt();
        $this->category = $product->getCategory() ? new CategoryDto($product->getCategory()) : null;
    }
}

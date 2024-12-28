<?php

namespace App\Dto;

class ProductDto
{
    public int $id;
    public string $name;
    public ?string $description;
    public array $prices;
    public int $stock;
    public string $locationAddress;
    public ?int $categoryId;
    public array $attributes;

    public function __construct(array $result)
    {
        $this->id = $result['id'];
        $this->name = $result['name'];
        $this->description = $result['description'];
        $this->prices = $result['price'];
        $this->stock = $result['stock'];
        $this->locationAddress = $result['locationAddress'];
        $this->categoryId = $result['categoryId'];
        $this->attributes = $result['attribute'];
    }
}

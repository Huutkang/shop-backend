<?php

namespace App\Dto;

class ProductDto
{
    public int $id;
    public string $name;
    public ?string $description;
    public ?int $prices=null;
    public ?int $stock=null;
    public ?string $locationAddress=null;
    public ?int $categoryId=null;
    public ?array $attributes=null;

    public function __construct(array $result)
    {
        $this->id = isset($result['id']) ? $result['id'] : $this->id;
        $this->name = isset($result['name']) ? $result['name'] : $this->name;
        $this->description = isset($result['description']) ? $result['description'] : $this->description;
        $this->prices = isset($result['price']) ? $result['price'] : $this->prices;
        $this->stock = isset($result['stock']) ? $result['stock'] : $this->stock;
        $this->locationAddress = isset($result['locationAddress']) ? $result['locationAddress'] : $this->locationAddress;
        $this->categoryId = isset($result['categoryId']) ? $result['categoryId'] : $this->categoryId;
        $this->attributes = isset($result['attribute']) ? $result['attribute'] : $this->attributes;
    }

}

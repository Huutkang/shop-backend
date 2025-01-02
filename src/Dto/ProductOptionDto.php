<?php

namespace App\Dto;

use App\Entity\ProductOption;


class ProductOptionDto
{
    public ?float $price;
    public ?int $stock;
    

    public function __construct(ProductOption $productOption)
    {
        $this->price = $productOption->getPrice();
        $this->stock = $productOption->getStock();
    }

}

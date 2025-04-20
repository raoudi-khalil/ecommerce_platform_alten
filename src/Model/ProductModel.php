<?php
// src/Model/ProductModel.php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "ProductModel",
    description: "DTO for creating/updating a product"
)]
class ProductModel
{
    #[Assert\NotBlank(message: "Code is required.")]
    #[OA\Property(example: "PRD-001", description: "Product code")]
    public ?string $code = null;

    #[Assert\NotBlank(message: "Name is required.")]
    #[OA\Property(example: "Wooden Chair", description: "Product name")]
    public ?string $name = null;

    #[Assert\Type("string")]
    #[OA\Property(example: "Solid wood chair for children", description: "Description")]
    public ?string $description = null;

    #[Assert\Url(message: "Image must be a valid URL.")]
    #[OA\Property(example: "https://example.com/image.jpg", description: "Image URL")]
    public ?string $image = null;

    #[Assert\Type("string")]
    #[OA\Property(example: "Furniture", description: "Category")]
    public ?string $category = null;

    #[Assert\Type("float")]
    #[Assert\GreaterThanOrEqual(0)]
    #[OA\Property(example: 49.99, description: "Price")]
    public ?float $price = null;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    #[OA\Property(example: 10, description: "Quantity")]
    public ?int $quantity = null;

    #[Assert\Type("string")]
    #[OA\Property(example: "INT-REF-123", description: "Internal reference")]
    public ?string $internalReference = null;

    #[Assert\Type("integer")]
    #[OA\Property(example: 5, description: "Shell ID")]
    public ?int $shellId = null;

    #[Assert\Choice(choices: ["INSTOCK", "LOWSTOCK", "OUTOFSTOCK"], message: "Invalid inventory status.")]
    #[OA\Property(example: "INSTOCK", enum: ["INSTOCK", "LOWSTOCK", "OUTOFSTOCK"], description: "Inventory status")]
    public ?string $inventoryStatus = null;

    #[Assert\Type("float")]
    #[Assert\Range(min: 0, max: 5)]
    #[OA\Property(example: 4.5, description: "Rating")]
    public ?float $rating = null;
}
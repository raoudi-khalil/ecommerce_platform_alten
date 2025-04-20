<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\InventoryStatus;
use OpenApi\Attributes as OA;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[OA\Schema(description: "Product entity used in the database and API", required: [])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(example: 1, description: "Unique identifier")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[OA\Property(example: "PRD-001", description: "Product code")]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[OA\Property(example: "Wooden Chair", description: "Name of the product")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[OA\Property(example: "Solid wood chair for children", description: "Detailed description")]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[OA\Property(example: "https://example.com/image.jpg", description: "URL of the product image")]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[OA\Property(example: "Furniture", description: "Product category")]
    private ?string $category = null;

    #[ORM\Column(nullable: true)]
    #[OA\Property(example: 49.99, description: "Product price")]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    #[OA\Property(example: 10, description: "Quantity available in stock")]
    private ?int $quantity = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[OA\Property(example: "INT-REF-123", description: "Internal reference")]
    private ?string $internalReference = null;

    #[ORM\Column(nullable: true)]
    #[OA\Property(example: 5, description: "Associated shell ID")]
    private ?int $shellId = null;

    #[ORM\Column(type: 'string', length: 255, enumType: InventoryStatus::class, nullable: true)]
    #[OA\Property(example: "IN_STOCK", description: "Inventory status")]
    private ?InventoryStatus $inventoryStatus = null;

    #[ORM\Column(nullable: true)]
    #[OA\Property(example: 4.7, description: "Average product rating")]
    private ?float $rating = null;

    #[ORM\Column(nullable: true)]
    #[OA\Property(example: 1713628800, description: "Creation timestamp (UNIX)")]
    private ?int $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[OA\Property(example: 1713700000, description: "Last update timestamp (UNIX)")]
    private ?int $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = time();
        $this->updatedAt = time();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getInternalReference(): ?string
    {
        return $this->internalReference;
    }

    public function setInternalReference(string $internalReference): static
    {
        $this->internalReference = $internalReference;

        return $this;
    }

    public function getShellId(): ?int
    {
        return $this->shellId;
    }

    public function setShellId(int $shellId): static
    {
        $this->shellId = $shellId;

        return $this;
    }

    public function getInventoryStatus(): ?string
    {
        return $this->inventoryStatus->value;
    }

    public function setInventoryStatus(InventoryStatus $inventoryStatus): static
    {
        $this->inventoryStatus = $inventoryStatus;
        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}

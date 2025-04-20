<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
#[OA\Schema(
    title: "CartItem",
    description: "An item added to a cart with quantity and timestamp",
    required: []
)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['cart:read'])]
    #[OA\Property(example: 1, description: "Unique identifier of the cart item")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: "The cart this item belongs to")]
    private ?Cart $cart = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cart:read'])]
    #[OA\Property(description: "The associated product")]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups(['cart:read'])]
    #[OA\Property(example: 2, description: "Quantity of the product added")]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups(['cart:read'])]
    #[OA\Property(example: 1713628800, description: "Timestamp when the product was added")]
    private ?int $addedAt = null;

    public function __construct()
    {
        $this->quantity = 1;
        $this->addedAt = time();
    }

    public function getId(): ?int { return $this->id; }

    public function getCart(): ?Cart { return $this->cart; }
    public function setCart(?Cart $cart): static { $this->cart = $cart; return $this; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $product): static { $this->product = $product; return $this; }

    public function getQuantity(): ?int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getAddedAt(): ?int { return $this->addedAt; }

    #[Groups(['cart:read'])]
    #[OA\Property(example: 99.98, description: "Total price = price x quantity")]
    public function getTotal(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }
}

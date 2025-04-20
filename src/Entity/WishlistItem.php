<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity]
#[OA\Schema(
    title: "WishlistItem",
    description: "A product saved in the user's wishlist",
    required: []
)]
class WishlistItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(example: 1, description: "Unique identifier of the wishlist item")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: "The wishlist that contains this item")]
    private ?Wishlist $wishlist = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: "The product added to the wishlist")]
    private ?Product $product = null;

    #[ORM\Column(type: 'integer')]
    #[OA\Property(example: 1713628800, description: "Timestamp when the product was added to the wishlist")]
    private int $addedAt;

    public function __construct()
    {
        $this->addedAt = time(); // UNIX timestamp
    }

    public function getId(): ?int { return $this->id; }

    public function getWishlist(): ?Wishlist { return $this->wishlist; }
    public function setWishlist(?Wishlist $wishlist): self { $this->wishlist = $wishlist; return $this; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $product): self { $this->product = $product; return $this; }

    public function getAddedAt(): int { return $this->addedAt; }
    public function setAddedAt(int $timestamp): self { $this->addedAt = $timestamp; return $this; }
}

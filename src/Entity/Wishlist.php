<?php

namespace App\Entity;

use App\Repository\WishlistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
#[OA\Schema(
    title: "Wishlist",
    description: "User's wishlist containing saved products",
    required: []
)]
class Wishlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(example: 1, description: "Unique identifier of the wishlist")]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'wishlist')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: "The user who owns the wishlist")]
    private ?User $user = null;

    #[ORM\OneToMany(
        mappedBy: 'wishlist',
        targetEntity: WishlistItem::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[OA\Property(
        description: "List of products saved in the wishlist",
        type: "array",
        items: new OA\Items(ref: "#/components/schemas/WishlistItem")
    )]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, WishlistItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }
}

<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;
use App\Entity\User;
use App\Entity\CartItem;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[OA\Schema(
    title: "Cart",
    description: "Shopping cart associated with a user",
    required: []
)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['cart:read'])]
    #[OA\Property(example: 1, description: "Cart ID")]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'cart')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: "User that owns the cart")]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartItem::class, orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['cart:read'])]
    #[OA\Property(type: "array", items: new OA\Items(ref: "#/components/schemas/CartItem"), description: "Items in the cart")]
    private Collection $items;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cart:read'])]
    #[OA\Property(example: 1713628800, description: "Timestamp of cart creation")]
    private ?int $createdAt = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cart:read'])]
    #[OA\Property(example: 1713700000, description: "Timestamp of last cart update")]
    private ?int $updatedAt = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = time();
        $this->updatedAt = time();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection { return $this->items; }

    public function addItem(CartItem $item): static
    {
        foreach ($this->items as $existingItem) {
            if ($existingItem->getProduct()->getId() === $item->getProduct()->getId()) {
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());
                $this->setUpdatedAt(time());
                return $this;
            }
        }

        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCart($this);
        }

        $this->setUpdatedAt(time());
        return $this;
    }

    public function removeItem(CartItem $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        $this->setUpdatedAt(time());
        return $this;
    }

    public function clearCart(): static
    {
        foreach ($this->items as $item) {
            $this->removeItem($item);
        }

        $this->setUpdatedAt(time());
        return $this;
    }

    public function getCreatedAt(): ?int { return $this->createdAt; }

    public function getUpdatedAt(): ?int { return $this->updatedAt; }

    public function setUpdatedAt(int $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    #[Groups(['cart:read'])]
    #[OA\Property(example: 3, description: "Total quantity of products in the cart")]
    public function getTotalQuantity(): int
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->getQuantity();
        }
        return $quantity;
    }

    #[Groups(['cart:read'])]
    #[OA\Property(example: 129.99, description: "Total price of the cart")]
    public function getTotalPrice(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getProduct()->getPrice() * $item->getQuantity();
        }
        return $total;
    }
}

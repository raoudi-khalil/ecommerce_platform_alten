<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
use App\Entity\Cart;
use App\Entity\Wishlist;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[OA\Schema(
    title: "User",
    description: "User entity for authentication and profile data",
    required: []
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(example: 1, description: "Unique identifier of the user")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Type(type: 'string', message: "Username must be a string.")]
    #[OA\Property(example: "john_doe", description: "Public username of the user")]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Type(type: 'string', message: "First name must be a string.")]
    #[OA\Property(example: "John", description: "First name of the user")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email(message: "Invalid email format.")]
    #[OA\Property(example: "john@example.com", description: "Email address (also used as login)")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Password is required.")]
    #[Assert\Length(min: 6, minMessage: "Password must be at least {{ limit }} characters long.")]
    #[OA\Property(example: "hashed_password_here", description: "User password (hashed)")]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Cart::class, cascade: ['persist', 'remove'])]
    #[OA\Property(description: "Associated shopping cart (one-to-one)")]
    private ?Cart $cart = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Wishlist::class, cascade: ['persist', 'remove'])]
    #[OA\Property(description: "Associated wishlist (one-to-one)")]
    private ?Wishlist $wishlist = null;

    // === GETTERS AND SETTERS ===

    public function getId(): ?int { return $this->id; }

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(?string $username): self { $this->username = $username; return $this; }

    public function getFirstname(): ?string { return $this->firstname; }
    public function setFirstname(?string $firstname): self { $this->firstname = $firstname; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(?string $password): self { $this->password = $password; return $this; }

    public function getUserIdentifier(): string { return $this->email ?? ''; }

    public function getRoles(): array { return ['ROLE_USER']; }

    public function eraseCredentials(): void {}

    public function getCart(): ?Cart { return $this->cart; }
    public function setCart(?Cart $cart): self
    {
        if ($cart === null && $this->cart !== null) {
            $this->cart->setUser(null);
        }
        if ($cart !== null && $cart->getUser() !== $this) {
            $cart->setUser($this);
        }
        $this->cart = $cart;
        return $this;
    }

    public function getWishlist(): ?Wishlist { return $this->wishlist; }
    public function setWishlist(?Wishlist $wishlist): self
    {
        if ($wishlist !== null && $wishlist->getUser() !== $this) {
            $wishlist->setUser($this);
        }
        $this->wishlist = $wishlist;
        return $this;
    }
}

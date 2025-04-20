<?php
// src/Model/CartModel.php

namespace App\Model;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class CartModel
{
    private CartRepository $cartRepository;
    private CartItemRepository $cartItemRepository;
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    // Constructor that does not require User entity
    public function __construct(
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    // Pass the User explicitly to the method
    public function getCurrentUserCart(User $user): ?Cart
    {
        if (!$user) {
            return null;
        }

        $cart = $user->getCart();  // Get the user's cart
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);  // Set the user to the cart
            $this->cartRepository->save($cart);  // Save the new cart
        }

        return $cart;
    }

    // Add a product to the user's cart
    public function addProductToCart(User $user, int $productId, int $quantity): Cart
    {
        $product = $this->productRepository->find($productId); // Find the product by ID
        if (!$product) {
            throw new \Exception('Product not found');
        }

        $cart = $this->getCurrentUserCart($user); // Get the current cart of the user

        // Check if the product is already in the cart
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $item->setQuantity($item->getQuantity() + $quantity); // Update quantity if product exists
                $this->entityManager->flush();
                return $cart;
            }
        }

        // If the product is not in the cart, add it
        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity($quantity);
        $cart->addItem($cartItem);

        $this->entityManager->persist($cartItem); // Persist the new cart item
        $this->entityManager->flush();

        return $cart;
    }

    // Remove an item from the user's cart
    public function removeItemFromCart(User $user, int $cartItemId): Cart
    {
        $cartItem = $this->cartItemRepository->find($cartItemId); // Find the cart item by ID
        if (!$cartItem) {
            throw new \Exception('Item not found');
        }

        $cart = $cartItem->getCart();
        $cart->removeItem($cartItem); // Remove the item from the cart
        $this->entityManager->remove($cartItem); // Remove the item from the database
        $this->entityManager->flush();

        return $cart;  // Return the updated cart
    }

    // Update the quantity of an item in the user's cart
    public function updateItemQuantity(User $user, int $cartItemId, int $quantity): Cart
    {
        if ($quantity <= 0) {
            return $this->removeItemFromCart($user, $cartItemId);  // Remove the item if quantity is 0 or less
        }

        $cartItem = $this->cartItemRepository->find($cartItemId); // Find the cart item by ID
        if (!$cartItem) {
            throw new \Exception('Item not found');
        }

        $cartItem->setQuantity($quantity); // Update the quantity of the item
        $cart = $cartItem->getCart();
        $this->entityManager->flush();

        return $cart;  // Return the updated cart
    }

    // Clear all items from the user's cart
    public function clearCart(User $user): Cart
    {
        $cart = $this->getCurrentUserCart($user);  // Get the user's current cart

        foreach ($cart->getItems() as $item) {
            $cart->removeItem($item);  // Remove the item from the cart
            $this->entityManager->remove($item);  // Remove the item from the database
        }

        $this->entityManager->flush();  // Save the changes
        return $cart;  // Return the cleared cart
    }
}

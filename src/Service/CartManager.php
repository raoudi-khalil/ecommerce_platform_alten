<?php
// src/Service/CartManager.php

namespace App\Service;

use App\Entity\Cart;
use App\Model\CartModel;
use App\Entity\User;

class CartManager
{
    private CartModel $cartModel;

    public function __construct(CartModel $cartModel)
    {
        $this->cartModel = $cartModel; // Store the CartModel instance
    }

    // Pass the User to CartModel methods
    public function getCart(User $user): ?Cart
    {
        return $this->cartModel->getCurrentUserCart($user); // Pass the user to CartModel
    }

    public function addProductToCart(User $user, int $productId, int $quantity): Cart
    {
        return $this->cartModel->addProductToCart($user, $productId, $quantity); // Pass the user to CartModel
    }

    public function removeItem(User $user, int $cartItemId): Cart
    {
        return $this->cartModel->removeItemFromCart($user, $cartItemId); // Pass the user to CartModel
    }

    public function updateItemQuantity(User $user, int $cartItemId, int $quantity): Cart
    {
        return $this->cartModel->updateItemQuantity($user, $cartItemId, $quantity); // Pass the user to CartModel
    }

    public function clearCart(User $user): Cart
    {
        return $this->cartModel->clearCart($user); // Pass the user to CartModel
    }
}

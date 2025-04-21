<?php

namespace App\Controller;

use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/cart', name: 'cart_')]
#[OA\Security(name: 'Bearer')]
class CartController extends AbstractController
{
    private CartManager $cartManager;

    public function __construct(CartManager $cartManager)
    {
        $this->cartManager = $cartManager; // Store the CartManager instance
    }

    #[Route('', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get the current user\'s cart',
        tags: ['Cart'],
        responses: [
            new OA\Response(response: 200, description: 'Cart retrieved successfully', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function getCart(): JsonResponse
    {
        $user = $this->getUser(); // Get the currently authenticated user

        // Pass the user to CartManager
        $cart = $this->cartManager->getCart($user);

        if (!$cart) {
            return $this->json(['message' => 'You must be logged in to access the cart'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($cart, Response::HTTP_OK, [], ['groups' => ['cart:read']]);
    }

    #[Route('/add/{productId}', name: 'add_product', methods: ['POST'])]
    #[OA\Post(
        summary: 'Add a product to the cart',
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'productId', in: 'path', description: 'Product ID to add to the cart', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product added successfully', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    public function addProduct(Request $request, int $productId): JsonResponse
    {
        $quantity = $request->request->getInt('quantity', 1); // Default to 1 if no quantity is specified
        $user = $this->getUser(); // Get the currently authenticated user

        try {
            $cart = $this->cartManager->addProductToCart($user, $productId, $quantity); // Pass the user to CartManager
            return $this->json([
                'message' => 'Product added to cart',
                'cart' => $cart
            ], Response::HTTP_OK, [], ['groups' => ['cart:read']]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/items/{cartItemId}', name: 'remove_item', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Remove an item from the cart',
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'cartItemId', in: 'path', description: 'Cart item ID to remove', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Item removed from cart'),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 404, description: 'Item not found')
        ]
    )]
    public function removeItem(int $cartItemId): JsonResponse
    {
        $user = $this->getUser(); // Get the currently authenticated user

        try {
            $cart = $this->cartManager->removeItem($user, $cartItemId); // Pass the user to CartManager
            return $this->json([
                'message' => 'Item removed from cart',
                'cart' => $cart
            ], Response::HTTP_OK, [], ['groups' => ['cart:read']]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/items/{cartItemId}', name: 'update_item', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Update the quantity of an item in the cart',
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'cartItemId', in: 'path', description: 'Cart item ID to update', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', example: 2)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Quantity updated successfully', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Invalid quantity'),
            new OA\Response(response: 404, description: 'Item not found')
        ]
    )]
    public function updateItem(Request $request, int $cartItemId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'] ?? 0;

        if ($quantity <= 0) {
            return $this->json(['error' => 'Quantity must be greater than 0'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser(); // Get the currently authenticated user

        try {
            $cart = $this->cartManager->updateItemQuantity($user, $cartItemId, $quantity); // Pass the user to CartManager
            return $this->json([
                'message' => 'Quantity updated',
                'cart' => $cart
            ], Response::HTTP_OK, [], ['groups' => ['cart:read']]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'clear', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Clear the cart',
        tags: ['Cart'],
        responses: [
            new OA\Response(response: 200, description: 'Cart cleared successfully'),
            new OA\Response(response: 400, description: 'Error clearing cart')
        ]
    )]
    public function clearCart(): JsonResponse
    {
        $user = $this->getUser(); // Get the currently authenticated user

        try {
            $cart = $this->cartManager->clearCart($user); // Pass the user to CartManager
            return $this->json([
                'message' => 'Cart cleared',
                'cart' => $cart
            ], Response::HTTP_OK, [], ['groups' => ['cart:read']]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}

<?php
// src/Controller/WishlistController.php

namespace App\Controller;

use App\Entity\Product;
use App\Service\WishlistManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/wishlist', name: 'wishlist_')]
#[OA\Security(name: 'Bearer')]
class WishlistController extends AbstractController
{
    public function __construct(
        private WishlistManager $wishlistManager
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/wishlist',
        summary: 'Get all items in the user’s wishlist',
        tags: ['Wishlist'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Wishlist items returned successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'productId', type: 'integer', example: 123),
                            new OA\Property(property: 'productName', type: 'string', example: 'Wooden Chair'),
                            new OA\Property(property: 'addedAt', type: 'integer', example: 1672531199)
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function list(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $items = $this->wishlistManager->listItems($user);

        $data = array_map(fn($item) => [
            'productId'   => $item->getProduct()->getId(),
            'productName' => $item->getProduct()->getName(),
            'addedAt'     => $item->getAddedAt(), // timestamp int
        ], $items);

        return new JsonResponse($data);
    }

    #[Route('/products/{id}', name: 'add', methods: ['POST'])]
    #[OA\Post(
        path: '/wishlist/products/{id}',
        summary: 'Add a product to the user’s wishlist',
        tags: ['Wishlist'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'The ID of the product to add'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product successfully added to wishlist',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'added')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 409,
                description: 'Product already in wishlist',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'already in wishlist')
                    ]
                )
            )
        ]
    )]
    public function add(Product $product): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $added = $this->wishlistManager->addProduct($user, $product);

        return new JsonResponse([
            'status' => $added ? 'added' : 'already in wishlist'
        ]);
    }

    #[Route('/products/{id}', name: 'remove', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/wishlist/products/{id}',
        summary: 'Remove a product from the user’s wishlist',
        tags: ['Wishlist'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'The ID of the product to remove'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product successfully removed from wishlist',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'removed')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found in wishlist',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'not found')
                    ]
                )
            )
        ]
    )]
    public function remove(Product $product): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $removed = $this->wishlistManager->removeProduct($user, $product);

        return new JsonResponse([
            'status' => $removed ? 'removed' : 'not found'
        ]);
    }
}
<?php
// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Model\ProductModel;
use App\Repository\ProductRepository;
use App\Service\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductManager $productManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private string $adminEmail
    ) {}

    private function isAdmin(): bool
    {
        return $this->getUser()?->getUserIdentifier() === $this->adminEmail;
    }

    #[Route('', methods: ['GET'], name: 'get_products')]
    #[OA\Get(summary: "Get all products", tags: ["Products"], responses: [
        new OA\Response(response: 200, description: "List of products", content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: Product::class)))),
        new OA\Response(response: 401, description: "Unauthorized")
    ])]
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $json = $this->serializer->serialize($products, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('', methods: ['POST'], name: 'create_product')]
    #[OA\Post(summary: "Create a product (admin only)", requestBody: new OA\RequestBody(
        required: true, content: new OA\JsonContent(ref: new Model(type: ProductModel::class))
    ), tags: ["Products"], responses: [
        new OA\Response(response: 201, description: "Created", content: new OA\JsonContent(ref: new Model(type: Product::class))),
        new OA\Response(response: 403, description: "Forbidden")
    ])]
    public function create(Request $request): JsonResponse
    {
        if (!$this->isAdmin()) {
            return new JsonResponse(['error' => 'Admin access only'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $model = $this->serializer->denormalize($data, ProductModel::class);

        $errors = $this->validator->validate($model);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 422);
        }

        $product = $this->productManager->createFromModel($model);
        $json = $this->serializer->serialize($product, 'json');
        return new JsonResponse($json, 201, [], true);
    }

    #[Route('/{id}', methods: ['PATCH'], name: 'update_product')]
    #[OA\Patch(summary: "Update a product (admin only)", parameters: [
        new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
    ], requestBody: new OA\RequestBody(
        required: true, content: new OA\JsonContent(ref: new Model(type: ProductModel::class))
    ), tags: ["Products"], responses: [
        new OA\Response(response: 200, description: "Updated", content: new OA\JsonContent(ref: new Model(type: Product::class))),
        new OA\Response(response: 403, description: "Forbidden"),
        new OA\Response(response: 404, description: "Not found")
    ])]
    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin()) {
            return new JsonResponse(['error' => 'Admin access only'], 403);
        }

        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $model = $this->serializer->denormalize($data, ProductModel::class);

        $errors = $this->validator->validate($model);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 422);
        }

        $product = $this->productManager->updateFromModel($product, $model);
        $json = $this->serializer->serialize($product, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get_product')]
    #[OA\Get(summary: "Get product by ID", parameters: [
        new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
    ], tags: ["Products"], responses: [
        new OA\Response(response: 200, description: "Found", content: new OA\JsonContent(ref: new Model(type: Product::class))),
        new OA\Response(response: 404, description: "Not found")
    ])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], 404);
        }

        $json = $this->serializer->serialize($product, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete_product')]
    #[OA\Delete(summary: "Delete product by ID (admin only)", parameters: [
        new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
    ], tags: ["Products"], responses: [
        new OA\Response(response: 200, description: "Deleted"),
        new OA\Response(response: 403, description: "Forbidden"),
        new OA\Response(response: 404, description: "Not found")
    ])]
    public function delete(int $id): JsonResponse
    {
        if (!$this->isAdmin()) {
            return new JsonResponse(['error' => 'Admin access only'], 403);
        }

        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], 404);
        }

        $this->productManager->delete($product);
        return new JsonResponse(['message' => 'Product deleted'], 200);
    }
}

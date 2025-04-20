<?php
// src/Service/ProductManager.php

namespace App\Service;

use App\Entity\Product;
use App\Enum\InventoryStatus;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\ProductModel;

class ProductManager
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createFromModel(ProductModel $model): Product
    {
        $product = new Product();
        $this->hydrate($product, $model);
        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    public function updateFromModel(Product $product, ProductModel $model): Product
    {
        $this->hydrate($product, $model);
        $this->em->flush();

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->em->remove($product);
        $this->em->flush();
    }

    private function hydrate(Product $product, ProductModel $model): void
    {
        $product->setCode($model->code);
        $product->setName($model->name);
        $product->setDescription($model->description);
        $product->setImage($model->image ?? '');
        $product->setCategory($model->category);
        $product->setPrice($model->price);
        $product->setQuantity($model->quantity);
        $product->setInternalReference($model->internalReference);
        $product->setShellId($model->shellId);
        $product->setRating($model->rating);

        if ($model->inventoryStatus !== null) {
            try {
                $product->setInventoryStatus(InventoryStatus::from($model->inventoryStatus));
            } catch (\ValueError) {
                throw new \InvalidArgumentException("Invalid inventoryStatus value. Must be one of: INSTOCK, LOWSTOCK, OUTOFSTOCK.");
            }
        }
    }
}

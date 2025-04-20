<?php
// src/Repository/CartItemRepository.php
namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function save(CartItem $cartItem, bool $flush = true): void
    {
        $this->getEntityManager()->persist($cartItem);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartItem $cartItem, bool $flush = true): void
    {
        $this->getEntityManager()->remove($cartItem);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
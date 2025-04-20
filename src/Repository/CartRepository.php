<?php
// src/Repository/CartRepository.php
namespace App\Repository;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findByUser(User $user): ?Cart
    {
        return $this->findOneBy(['user' => $user]);
    }

    public function save(Cart $cart, bool $flush = true): void
    {
        $this->getEntityManager()->persist($cart);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
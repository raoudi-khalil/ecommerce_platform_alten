<?php
// src/Service/WishlistManager.php
namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Entity\WishlistItem;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;

class WishlistManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private WishlistRepository $wishlistRepo
    ) {}

    public function getOrCreateWishlist(User $user): Wishlist
    {
        $wishlist = $this->wishlistRepo->findOneByUser($user);
        if (!$wishlist) {
            $wishlist = new Wishlist();
            $wishlist->setUser($user);
            $this->em->persist($wishlist);
            $this->em->flush();
        }
        return $wishlist;
    }

    public function addProduct(User $user, Product $product): bool
    {
        $wishlist = $this->getOrCreateWishlist($user);
        foreach ($wishlist->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()){
                return false;
            }
        }

        $item = new WishlistItem();
        $item->setWishlist($wishlist);
        $item->setProduct($product);

        $this->em->persist($item);
        $this->em->flush();
        
        return true;
    }

    public function removeProduct(User $user, Product $product): bool
    {
        $wishlist = $this->wishlistRepo->findOneByUser($user);
        if (!$wishlist) {
             return false;
        }

        foreach ($wishlist->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $wishlist->getItems()->removeElement($item);
                $this->em->remove($item);
                $this->em->flush();
                return true;
            }
        }

        return false;
    }

    public function listItems(User $user): array
    {
        $wishlist = $this->wishlistRepo->findOneByUser($user);
        return $wishlist ? $wishlist->getItems()->toArray() : [];
    }
}

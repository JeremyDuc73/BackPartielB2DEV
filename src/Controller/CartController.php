<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api')]
class CartController extends AbstractController
{
    #[Route('/cart', methods: ['GET'])]
    public function cart(CartService $cartService)
    {
        return $this->json($cartService->getCart(), 200, [], ['groups'=>'cart']);
    }

    #[Route('/cart/total', methods: ['GET'])]
    public function total(CartService $cartService)
    {
        return $this->json($cartService->getTotal(), 200);
    }

    #[Route('/cart/add/{id}/{quantity}', methods: ['POST'])]
    public function addProductToCart(Product $product, $quantity, CartService $cartService)
    {
        $cartService->addProduct($product, $quantity);
        return $this->json($quantity." * ".$product->getName()." successfully add to cart", 200);
    }

    #[Route('/cart/remove/{id}', methods: ['POST'])]
    public function removeOneProductFromCart(CartService $cartService, Product $product)
    {
        $cartService->removeProduct($product);
        return $this->json("One ".$product->getName()." successfully removed from cart", 200);
    }

    #[Route('/cart/removewhole/{id}', methods: ['POST'])]
    public function removeWholeSameProductsFromCart(CartService $cartService, Product $product)
    {
        $cartService->removeProductRow($product);
        return $this->json("All ".$product->getName()." successfully removed from cart", 200);
    }

    #[Route('/cart/empty', methods: ['POST'])]
    public function emptyCart(CartService $cartService)
    {
        $cartService->emptyCart();
        return $this->json("Cart is now empty");
    }
}

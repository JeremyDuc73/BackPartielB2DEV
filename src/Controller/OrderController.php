<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Attribute\Ignore;

class OrderController extends AbstractController
{
    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function allOrders(OrderRepository $repository)
    {
        return $this->render('order/index.html.twig', ['orders'=>$repository->findAll()]);
    }
    #[Route('/api/myorders', methods: ['GET'])]
    public function getMyOrders()
    {
        $myOrders = $this->getUser()->getProfile()->getOrders();
        return $this->json($myOrders, 200, [], ['groups'=>'myorders']);
    }

    #[Route('/api/makeorder', methods: ['POST'])]
    public function makeOrder(CartService $cartService, EntityManagerInterface $manager)
    {
        $order = new Order();
        $order->setProfile($this->getUser()->getProfile());
        $order->setStatus(2);
        $order->setTotal($cartService->getTotal());
        $order->setCreatedAt(new \DateTimeImmutable());

        foreach ($cartService->getCart() as $item)
        {
            $orderItem = new OrderItem();
            $orderItem->setProduct($item['product']);
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setOfOrder($order);
            $manager->persist($orderItem);
        }
        $manager->persist($order);
        $manager->flush();
        $cartService->emptyCart();
        return $this->json("Your order is made", 200);
    }
}

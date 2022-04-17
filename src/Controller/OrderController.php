<?php

namespace App\Controller;

use App\Entity\Dishe;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(OrderRepository $orderRepository): Response
    {

        $order = $orderRepository->findBy(
            ['tab' => 'table 1']
        );

        return $this->render('order/index.html.twig', [
            'order' => $order
        ]);
    }


    #[Route('/ord/{id}', name: 'ord')]
    public function order(Dishe $dishe, ManagerRegistry $doctrine)
    {
        $order = new Order();
        $order->setTab("table 1");
        $order->setName($dishe->getName());
        $order->setOrderNumber($dishe->getId());
        $order->setPrice($dishe->getPrice());
        $order->setStatus("Open");

        $em = $doctrine->getManager();
        $em->persist($order);
        $em->flush();

        $this->addFlash('order', $order->getName(). " was added to the order");

        return $this->redirect($this->generateUrl('menu'));
    }

    #[Route('/status/{id},{status}', name: 'status')]
    public function status($id, $status, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $order = $em->getRepository(Order::class)->find($id);

        $order->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('order'));
        
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, OrderRepository $or, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $order = $or->find($id);
        $em->remove($order);
        $em->flush();

        return $this->redirect($this->generateUrl('order'));
    }

}

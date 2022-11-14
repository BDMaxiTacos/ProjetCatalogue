<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderArticle;
use App\Entity\State;
use App\Entity\Creneau;
use App\Repository\OrderRepository;
use App\Repository\OrderArticleRepository;
use App\Repository\ShopRepository;
use App\Form\DatePickerOrderType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminShopController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param OrderRepository $orderRepository
     * @param ShopRepository $shopRepository
     * @return Response
     * Show all orders
     */
    #[Route('/commandes', name: 'orders')]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $shops = array();
        $orders = $orderRepository->findByUser($user);

        foreach ($orders as $order) {
            $shop = $order->getShop();
            if (!in_array($shop, $shops)) {
                $shops[] = $shop;
            }
        }

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'shops' => $shops,
        ]);
    }

    /**
     * @param Order $order
     * @return Response
     * Cancel Order
     */
    #[Route('/commandes/cancel/{id}', name: 'order.cancel')]
    public function cancel(Order $order): Response
    {
        $stateRepository = $this->getDoctrine()->getRepository(State::class);
        $state = $stateRepository->findByName("Commande annulée")[0];

        $orderArticleRepository = $this->getDoctrine()->getRepository(OrderArticle::class);
        $oas = $orderArticleRepository->getOAs($order);

        foreach ($oas as $oa){
            $oa->getArticle()->setStockAvailable($oa->getArticle()->getStockAvailable() + $oa->getQuantity());
        }

        $order->setState($state);
        $this->em->flush();
        $this->addFlash('success', 'Votre commande a été annulée');
        return $this->redirectToRoute('orders');
    }


    /**
     * @param Order $order
     * @param OrderArticleRepository $orderArticleRepository
     * @param Request $request
     * @return Response
     * @throws \Exception
     * Show Order details
     */
    #[Route('/commandes/{id}', name: 'order.show')]
    public function show(Order $order, OrderArticleRepository $orderArticleRepository, Request $request): Response
    {
        $quantities = array();
        $articles= array();
        $oas = $orderArticleRepository->getOAs($order);

        foreach($oas as $oa){
            $order->addOrderArticle($oa);
            $quantities[$oa->getArticle()->getId()] = $oa->getQuantity();
            $articles[] = $oa->getArticle();
        }

        $creneau = new Creneau();

        $form = $this->createForm(DatePickerOrderType::class,$creneau);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $order->setDateRetrieve(new \DateTime($creneau->getDateRetrieve(), new \DateTimeZone('Europe/Paris')));
            $this->em->persist($order);
            $this->em->flush();
        }

        return $this->render('order/show.html.twig',[
            'order' => $order,
            'quantities' => $quantities,
            'articles' => $articles,
            'form' => $form->createView(),
        ]);
    }
}

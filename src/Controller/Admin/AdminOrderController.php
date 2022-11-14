<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\State;
use App\Repository\StateRepository;
use App\Repository\OrderArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class AdminOrderController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminOrderController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Order $order
     * @param StateRepository $stateRepository
     * @param Request $request
     * @return Response
     * Set order ready
     */
    #[Route('/admin/commandes/ready/{id}', name: 'admin.order.ready')]
    public function ready(Order $order, StateRepository $stateRepository, Request $request, MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tacos.me', 'Product site'))
            ->to($order->getShop()->getEmail())
            ->subject('Order ready')
            ->htmlTemplate('admin/order/_ready.html.twig');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'Erreur d\'envoi dans le mail');
            return $this->redirectToRoute('admin.order.index');
        }

        $state = $request->get('state');
        $newState = $stateRepository->findByName($state)[0];
        $order->setState($newState);
        $this->em->flush($order);
        $this->addFlash('success', 'La commande a vu son état changé');

        return $this->redirectToRoute('admin.order.index');
    }

    /**
     * @param Order $order
     * @param StateRepository $stateRepository
     * @param Request $request
     * @return Response
     * Set order retrieved
     */
    #[Route('/admin/commandes/taken/{id}', name: 'admin.order.taken')]
    public function taken(Order $order, StateRepository $stateRepository, Request $request, MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tacos.me', 'Product site'))
            ->to($order->getShop()->getEmail())
            ->subject('Order retrieved')
            ->htmlTemplate('admin/order/_taken.html.twig');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'Erreur d\'envoi dans le mail');
            return $this->redirectToRoute('admin.order.index');
        }

        $state = $request->get('state');
        $newState = $stateRepository->findByName($state)[0];
        $order->setState($newState);
        $this->em->flush($order);
        $this->addFlash('success', 'La commande a vu son état changé');

        return $this->redirectToRoute('admin.order.index');
    }

    /**
     * @param Order $order
     * @param OrderArticleRepository $orderArticleRepository
     * @param Request $request
     * @return Response
     * @throws \Exception
     * Show Order details
     */
    #[Route('/admin/commandes/{id}', name: 'admin.order.show')]
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

        return $this->render('admin/order/show.html.twig',[
            'order' => $order,
            'quantities' => $quantities,
            'articles' => $articles,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * Delete product of user's cart
     */
    #[Route('/admin/commandes/article/delete', name: 'admin.order.article.delete')]
    public function removeArticle(Request $request): Response
    {
        $order = $request->query->get('order');
        $order = $this->getDoctrine()->getRepository(Order::class)->findCartById($order);

        $articles = array();
        $quantities = array();
        $order = $order[0];

        $orderArticleRepository = $this->getDoctrine()->getRepository(OrderArticle::class);

        $oas = $orderArticleRepository->getOAs($order);

        foreach($oas as $oa){
            $order->addOrderArticle($oa);
            $quantities[$oa->getArticle()->getId()] = $oa->getQuantity();
            $articles[] = $oa->getArticle();
        }

        $article = $request->query->get('id');
        $article = $this->getDoctrine()->getRepository(Article::class)->findById($order->getShop(), $article);
        $orderArticle = $orderArticleRepository->findByOrderAndArticle($order, $article);


        $article = $article[0];
        if(!empty($orderArticle)){
            $orderArticle = $orderArticle[0];
            if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->get('_token'))) {
                $order->removeOrderArticle($orderArticle);
                $this->em->remove($orderArticle);
                $this->em->persist($order);
                $this->em->flush();
            } else {
                $this->addFlash('error', 'Suppression impossible');
                return $this->redirectToRoute('admin.order.show', [
                    'order' => $order,
                ]);
            }
        }

        $this->addFlash('success', 'Votre article a bien été supprimé');
        return $this->redirectToRoute('admin.order.show', [
            "order" => $order,
        ]);
    }
}

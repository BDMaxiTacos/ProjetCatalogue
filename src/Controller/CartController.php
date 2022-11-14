<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Article;
use App\Entity\OrderArticle;
use App\Entity\Shop;
use App\Entity\State;
use App\Repository\OrderArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * CartController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return Response
     * Display content of user's cart
     */
    #[Route('/panier', name: 'cart.index')]
    public function index(Request $request): Response
    {
        $shopRepository = $this->getDoctrine()->getRepository(Shop::class);

        $idshop = $request->query->get('id');
        $shop = $shopRepository->findById($idshop);
        if(empty($shop)){
            $shop = $shopRepository->findAllVisible()[0];
        }else{
            $shop = $shop[0];

        }

        $cartRepo = $this->getDoctrine()->getRepository(Order::class);

        $articles = array();
        $quantities = array();
        $cart = $cartRepo->findCartByShop($shop, $this->getUser());
        if(empty($cart)){
            $cart = new Order();
            $cart->setShop($shop);
            $cart->setUser($this->getUser());
            $cart->setDateOrdered(new \DateTime);

            $cart->setState($this->getDoctrine()->getRepository(State::class)->findById(1)[0]);


            $this->em->flush();
        }else{
            $cart = $cart[0];

            $orderArticleRepository = $this->getDoctrine()->getRepository(OrderArticle::class);

            $oas = $orderArticleRepository->getOAs($cart);

            foreach($oas as $oa){
                $cart->addOrderArticle($oa);
                $quantities[$oa->getArticle()->getId()] = $oa->getQuantity();
                $articles[] = $oa->getArticle();
            }
        }

        $args = [
            'cart' => $cart,
            'length_article' => count($cart->getOrderArticles()),
            'quantities' => $quantities,
            'articles' => $articles,
        ];

        if(!empty($request->query->get('mode')) && $request->query->get('mode') === 'edit'){
            $args['mode'] = 'edit';
        }

        return $this->render('cart/index.html.twig', $args);
    }

    /**
     * @param Request $request
     * @return Response
     * Delete product of user's cart
     */
    #[Route('/panier/article/delete', name: 'cart.article.delete')]
    public function deleteArticle(Request $request): Response
    {
        $cart = $request->query->get('order');
        $cart = $this->getDoctrine()->getRepository(Order::class)->findCartById($cart);

        $articles = array();
        $quantities = array();
        $cart = $cart[0];

        $orderArticleRepository = $this->getDoctrine()->getRepository(OrderArticle::class);

        $oas = $orderArticleRepository->getOAs($cart);

        foreach($oas as $oa){
            $cart->addOrderArticle($oa);
            $quantities[$oa->getArticle()->getId()] = $oa->getQuantity();
            $articles[] = $oa->getArticle();
        }

        $article = $request->query->get('id');
        $article = $this->getDoctrine()->getRepository(Article::class)->findById($cart->getShop(), $article);
        $orderArticle = $orderArticleRepository->findByOrderAndArticle($cart, $article);


        $article = $article[0];
        if(!empty($orderArticle)){
            $orderArticle = $orderArticle[0];
            if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->get('_token'))) {
                $cart->removeOrderArticle($orderArticle);
                $this->em->remove($orderArticle);
                $this->em->persist($cart);
                $this->em->flush();
            } else {
                $this->addFlash('error', 'Suppression impossible');
                return $this->render('cart/index.html.twig', [
                    'cart' => $cart,
                    'length_article' => count($cart->getOrderArticles()),
                    'quantities' => $quantities,
                    'articles' => $articles,
                ]);
            }
        }

        $this->addFlash('success', 'Votre article a bien été supprimé');
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'length_article' => count($cart->getOrderArticles()),
            'quantities' => $quantities,
            'articles' => $articles,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * Validate user's cart
     */
    #[Route('/panier/validate', name: 'cart.validate')]
    public function validate(Request $request): Response
    {
        $cart = $request->query->get('cart');
        $cart = $this->getDoctrine()->getRepository(Order::class)->findCartById($cart)[0];

        $orderArticles = $this->getDoctrine()->getRepository(OrderArticle::class)->getOAs($cart);

        foreach($orderArticles as $orderArticle){
            $article = $orderArticle->getArticle();
            $stock = $article->getStockAvailable();
            $quantity = $orderArticle->getQuantity();
            if($stock >= $quantity){
                $article->setStockAvailable($stock-$orderArticle->getQuantity());
            }else{
                $article->setStockAvailable(0);
            }
        }

        $stateRepository = $this->getDoctrine()->getRepository(State::class);

        $state = $stateRepository->findByName("Commande en cours")[0];

        $cart->setState($state);

        $this->em->flush();

        $this->addFlash('success', 'Votre panier a été validé');
        return $this->redirectToRoute('articles', [
            'idshop' => $cart->getShop()->getId()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * Validate user's cart
     */
    #[Route('/panier/{cart}/{id}', name: 'cart.edit')]
    public function editQuantity(Order $cart, Article $article, Request $request, OrderArticleRepository $orderArticleRepository): Response
    {
        $oa = $orderArticleRepository->findByOrderAndArticle($cart,$article);

        if(!empty($oa)){
            //TODO
            $oa = $oa[0];
            $formerQuantity = $oa->getQuantity();
            $diff = $request->request->get('quantity'.$oa->getArticle()->getId()) - $formerQuantity;
            $oa->setQuantity($request->request->get('quantity'.$oa->getArticle()->getId()));
            $oa->getArticle()->setStockAvailable($formerQuantity-abs($diff));
            $this->addFlash('success', 'La quantité a été modifiée');
        }else{
            $this->addFlash('error', 'La quantité n\'a pas pu être modifiée');
        }

        $this->em->persist($oa);
        $this->em->flush();

        return $this->redirectToRoute('cart.index');
    }


}

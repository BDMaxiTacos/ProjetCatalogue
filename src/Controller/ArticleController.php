<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\State;
use App\Entity\FilterArticle;
use App\Entity\OrderArticle;
use App\Repository\ArticleRepository;
use App\Repository\ShopRepository;
use App\Form\FilterArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * ArticleController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param ShopRepository $shopRepository
     * @return Response
     * List products from chosen shop
     */
    #[Route('/articles', name: 'articles')]
    public function index(Request $request, ArticleRepository $articleRepository, ShopRepository $shopRepository): Response
    {

        $idshop = $request->query->get('idshop');
        $shop = $shopRepository->findById($idshop);
        if(!empty($shop)){
            $shop = $shop[0];
        }else{
            $this->addFlash('error', 'Le shop n\'existe pas');
            return $this->redirectToRoute('home');
        }

        $filter = new FilterArticle();
        $form = $this->createForm(FilterArticleType::class,$filter);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $search = $request->request->get('filter_article')['search'];
        }

        $mesArticles = $articleRepository->getArticlesRequested($search ?? null, $shop);

        return $this->render('article/index.html.twig', [
            'articles' => $mesArticles,
            'shop' => $shop,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @param Request $request
     * @param ShopRepository $shopRepository
     * @return Response
     * Display products details
     */
    #[Route('/articles/{id}', name: 'articles.show')]
    public function show(Article $article, Request $request, ShopRepository $shopRepository): Response
    {
        $idshop = $request->query->get('idshop');
        $shop = $shopRepository->findById($idshop);
        if(!empty($shop)){
            $shop = $shop[0];
        }else{
            $this->addFlash('error', 'Le shop n\'existe pas');
            return $this->redirectToRoute('home');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'shop' => $shop
        ]);
    }

    /**
     * @param Article $newArticle
     * @param Request $request
     * @param ShopRepository $shopRepository
     * @param ArticleRepository $articleRepository
     * @return Response
     * Add chosen product to cart
     */
    #[Route('/articles/addtocart/{id}', name: 'article.cart.add')]
    public function addToCart(Article $newArticle, Request $request, ShopRepository $shopRepository, ArticleRepository $articleRepository): Response
    {
        $idshop = $request->query->get('idshop');
        $shop = $shopRepository->findById($idshop);
        if(!empty($shop)){
            $shop = $shop[0];
        }else{
            $this->addFlash('error', 'Le shop n\'existe pas');
            return $this->redirectToRoute('home');
        }

        $cartRepo = $this->getDoctrine()->getRepository(Order::class);

        $cart = $cartRepo->findCartByShop($shop, $this->getUser());
        if(empty($cart)){
            $cart = new Order();
            $cart->setShop($shop);
            $cart->setUser($this->getUser());
            $cart->setDateOrdered(new \DateTime);
            $cart->setState($this->getDoctrine()->getRepository(State::class)->findById(1)[0]);


            $this->em->persist($cart);
            $this->em->flush();
        }else{
            $cart = $cart[0];
        }

        $orderArticleRepository = $this->getDoctrine()->getRepository(OrderArticle::class);

        $oaExists = $orderArticleRepository->getOAExists($cart->getId(), $newArticle->getId());
        $articles = $articleRepository->getArticlesRequested(null, $shop);

        $filter = new FilterArticle();

        $form = $this->createForm(FilterArticleType::class,$filter);
        $form->handleRequest($request);
        if(!empty($oaExists)){
            $this->addFlash('error', 'Article déjà dans votre panier');
            return $this->render('article/index.html.twig', [
                'articles' => $articles,
                'shop' => $shop,
                'idshop' => $shop->getId(),
                'form' => $form->createView()
            ]);
        }

        $quantity = $request->request->get('quantity');

        $newOA = new OrderArticle();
        $newOA->setOrder($cart);
        $newOA->setArticle($newArticle);
        $newOA->setQuantity($quantity);

        $this->em->persist($newOA);
        $this->em->flush();

        $this->addFlash('success', 'Votre article a bien été ajouté');
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'shop' => $shop,
            'idshop' => $shop->getId(),
            'form' => $form->createView()
        ]);
    }
}

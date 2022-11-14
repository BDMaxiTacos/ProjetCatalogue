<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use App\Entity\Article;
use App\Entity\Order;
use App\Entity\State;
use App\Entity\User;
use App\Entity\FilterArticleAdmin;
use App\Entity\FilterArticle;
use App\Entity\FilterOrderAdmin;
use App\Entity\FilterShopAdmin;
use App\Entity\FilterUserAdmin;
use App\Repository\ShopRepository;
use App\Repository\ArticleRepository;
use App\Repository\StateRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Form\AdminFilterArticleType;
use App\Form\FilterArticleType;
use App\Form\AdminFilterShopType;
use App\Form\AdminFilterOrderType;
use App\Form\AdminFilterUserType;
//use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
//use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     * @return Response
     * Home admin
     */
    public function index(): Response
    {
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $shopRepository = $this->getDoctrine()->getRepository(Shop::class);
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $orders = $orderRepository->findAllVisible();
        $shops = $shopRepository->findAllVisible();
        $articles = $articleRepository->getArticlesRequested();
        $users = $userRepository->findAllUsers();

        return $this->render('admin/index.html.twig',[
            'count_orders' => count($orders),
            'count_shops' => count($shops),
            'count_articles' => count($articles),
            'count_users' => count($users),
        ]);
    }

    /**
     * @param Request $request
     * @param ShopRepository $shopRepository
     * @return Response
     * Management shops
     */
    #[Route('/admin/magasins', name: 'admin.shop.index')]
    public function indexShop(Request $request, ShopRepository $shopRepository): Response
    {
        $filter = new FilterShopAdmin();

        $form = $this->createForm(AdminFilterShopType::class,$filter);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $myShops = $shopRepository->findByPartName($request->get('admin_filter_shop')['search']);
        }else{
            $myShops = $shopRepository->findAllVisible();
        }

        return $this->render('admin/shop/index.html.twig',[
            'shops' => $myShops,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param OrderRepository $orderRepository
     * @param StateRepository $stateRepository
     * @param Request $request
     * @return Response
     * Management orders
     */
    #[Route('/admin/commandes', name: 'admin.order.index')]
    public function indexOrders(OrderRepository $orderRepository, StateRepository $stateRepository, Request $request): Response
    {
        $filter = new FilterOrderAdmin();

        $form = $this->createForm(AdminFilterOrderType::class,$filter);
        $form->handleRequest($request);

        $state = null;
        if($form->isSubmitted() && $form->isValid()) {
            $state = (int)$request->get('admin_filter_order')['state'];
            $state = $stateRepository->findById($state);
            if(!empty($state)){
                $state = $state[0];
            }else{
                $state = null;
            }
        }

        $myOrders = $orderRepository->findByState($state);

        return $this->render('admin/order/index.html.twig',[
            'orders' => $myOrders,
            'statut' => $state,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @return Response
     * Management products
     */
    #[Route('/admin/articles', name: 'admin.article.index')]
    public function indexArticles(ArticleRepository $articleRepository, Request $request): Response
    {
        $filterVar = new FilterArticleAdmin();
        $formFilter = $this->createForm(AdminFilterArticleType::class,$filterVar);
        $formFilter->handleRequest($request);

        $searchVar = new FilterArticle();
        $formSearch = $this->createForm(FilterArticleType::class,$searchVar);
        $formSearch->handleRequest($request);


        if($formFilter->isSubmitted() && $formFilter->isValid()) {
            $shop = $request->get('admin_filter_article')['shop'];
        }

        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $search = $request->get('filter_article')['search'];
        }

        $myArticles = $articleRepository->getArticlesRequested($search ?? null,$shop ?? null);

        return $this->render('admin/article/index.html.twig', [
            'articles' => $myArticles,
            'formFilter' => $formFilter->createView(),
            'formSearch' => $formSearch->createView(),
        ]);
    }

    /**
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @return Response
     * Management products
     */
    #[Route('/admin/utilisateurs', name: 'admin.user.index')]
    public function indexUsers(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $filter = new FilterUserAdmin();

        $form = $this->createForm(AdminFilterUserType::class,$filter);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->findByPartUsername($request->get('admin_filter_user')['search'], $user);
        }else{
            $users = $userRepository->findAllUsers($user);
        }

        foreach ($users as $user){
            dump([$user->getUsername(), $user->getPassword()]);
        }
        return $this->render('admin/user/index.html.twig',[
            'users' => $users,
            'form' => $form->createView()
        ]);
    }
}

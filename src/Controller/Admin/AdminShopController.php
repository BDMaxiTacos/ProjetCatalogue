<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use App\Form\ShopFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminShopController extends AbstractController
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
     * @param Request $request
     * @return Response
     * Add shop to list
     */
    #[Route('/admin/magasins/new', name:'admin.shop.new')]
    public function new(Request $request): Response
    {
        $shop = new Shop();

        $form = $this->createForm(ShopFormType::class,$shop);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($shop);
            $this->em->flush();
            $this->addFlash('success', 'Votre magasin a bien été ajouté');
            return $this->redirectToRoute('admin.shop.index');
        }

        return $this->render('admin/shop/new.html.twig', [
            'shop' => $shop,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Shop $shop
     * @param Request $request
     * @return Response
     * Delete shop from list
     */
    #[Route('/admin/magasins/del/{id}', name: 'admin.shop.delete', methods: "GET|POST|DELETE")]
    public function delete(Shop $shop, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete' . $shop->getId(), $request->get('_token'))){
            $this->em->remove($shop);
            $this->em->flush();
            $this->addFlash('success', 'Votre magasin a bien été supprimé');
        }
        return $this->redirectToRoute('admin.shop.index');
    }


    /**
     * @param Shop $shop
     * @param Request $request
     * @return Response
     * Edit shop
     */
    #[Route('/admin/magasins/edit/{id}', name: 'admin.shop.edit', methods: "GET|POST")]
    public function edit(Shop $shop, Request $request): Response
    {
        $form = $this->createForm(ShopFormType::class,$shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Votre magasin a bien été édité');
            return $this->redirectToRoute('admin.shop.index');
        }

        return $this->render('admin/shop/edit.html.twig', [
            'shop' => $shop,
            'form' => $form->createView()
        ]);
    }
}
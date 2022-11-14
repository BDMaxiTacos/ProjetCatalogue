<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminArticleController constructor.
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
    #[Route('/admin/articles/new', name: 'admin.article.new')]
    public function new(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleFormType::class,$article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($article);
            $this->em->flush();
            $this->addFlash('success', 'Votre article a bien été ajouté');
            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Article $article
     * @param Request $request
     * @return Response
     * Delete shop from list
     */
    #[Route('/admin/articles/del/{id}', name: 'admin.article.delete', methods: "GET|POST|DELETE")]
    public function delete(Article $article, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete' . $article->getId(), $request->get('_token'))){
            $this->em->remove($article);
            $this->em->flush();
            $this->addFlash('success', 'Votre article a bien été supprimé');
        }
        return $this->redirectToRoute('admin.article.index');
    }


    /**
     * @param Article $article
     * @param Request $request
     * @return Response
     * Edit shop
     */
    #[Route('/admin/articles/edit/{id}', name: 'admin.article.edit', methods: "GET|POST")]
    public function edit(Article $article, Request $request): Response
    {
        $form = $this->createForm(ArticleFormType::class,$article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Votre article a bien été édité');
            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);

    }
}

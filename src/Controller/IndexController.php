<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{


   /* /**
     * @Route("/", name="home")
     */
    // Site's home
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\ShopRepository;
use App\Security\EmailVerifier;
use App\Form\ContactFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{

    /**
     * @param ShopRepository $shopRepository
     * @return Response
     * List shops available
     */
    #[Route('/magasins', name: 'shops')]
    public function index(ShopRepository $shopRepository): Response
    {
        $mesShops = $shopRepository->findAllVisible();

        return $this->render('shop/index.html.twig', [
            'shops' => $mesShops,
        ]);
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * Contact shop form
     */
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $emailshop = $request->query->get('email');
        $nameshop = $request->query->get('shopname');

        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@tacos.me', $nameshop))
                ->to('tristan.tsti2d5@gmail.com')
                ->to($emailshop)
                ->subject('Please Confirm your Email')
//                ->html('<p>Contect sur le magasin</p>');
                ->htmlTemplate('shop/contact_email.html.twig');

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', 'Erreur d\'envoi dans le mail');
                return $this->redirectToRoute('contact');
            }

            return $this->render('index/index.html.twig', []);
        }

        return $this->render('shop/contact.html.twig', [
            'email_shop' => $emailshop,
            'name_shop' => $nameshop,
            'form_contact' => $form->createView(),
        ]);
    }
}

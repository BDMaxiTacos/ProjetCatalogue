<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param EmailVerifier $emailVerifier
     * @param AuthenticationManagerInterface $authenticationManager
     * RegistrationController contstructor
     */
    public function __construct(TokenStorageInterface $tokenStorage, EmailVerifier $emailVerifier, AuthenticationManagerInterface $authenticationManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasherInterface
     * @return Response
     * Register user in BDD
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            $username = $form->get('username')->getData();

            // encode the plain password
            $password = $userPasswordHasherInterface->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword(
                $password
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@tacos.me', 'Product site'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            $roles = $user->getRoles();
            if(!in_array('ROLE_USER', $roles)){
                $roles[] = 'ROLE_USER';
            }

            $user->setRoles($roles);

            $unauthenticatedToken = new UsernamePasswordToken(
                $user,
                $password,
                'main',
                $user->getRoles()
            );

            $authenticatedToken = $this
                ->authenticationManager
                ->authenticate($unauthenticatedToken);

            $this->tokenStorage->setToken($authenticatedToken);
            $this->get('session')->set('_security_main', serialize($authenticatedToken));

            $route = in_array('ROLE_ADMIN', $user->getRoles()) ? 'admin' : 'home';

            return $this->redirectToRoute($route);

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * Verify email entered
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}

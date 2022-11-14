<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

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
     * @param AuthenticationManagerInterface $authenticationManager
     * AuthController constructor
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param UserPasswordHasherInterface $hasher
     * @param Request $request
     * @return Response
     * Login user and redirect if admin or user
     */
    public function login(AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $hasher, Request $request): Response
    {
        if($request->get('username')){
            $username = $request->get('username');

            $user = $this->getDoctrine()->getRepository(User::class)->findByUsername($username);
            if (!empty($user)) {
                $user = $user[0];
                if($password = $hasher->isPasswordValid($user, $request->get('password'))) {
                    $unauthenticatedToken = new UsernamePasswordToken(
                        $user,
                        $user->getPassword(),
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
            }
            $this->addFlash('error', "Nom d'utilisateur ou mot de passe incorrect");
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $username ?? $lastUsername,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * Logout user
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

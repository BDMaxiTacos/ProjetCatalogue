<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Form\AdminUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUserController extends AbstractController
{

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminUserController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return Response
     * Add user to list
     */
    #[Route('/admin/utilisateurs/new', name:'admin.user.new')]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();

        $form = $this->createForm(AdminUserFormType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $password = $userPasswordHasherInterface->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($password);

            $roles = $user->getRoles();
            if(!in_array('ROLE_USER', $roles)){
                $roles[] = 'ROLE_USER';
            }

            if($form->get('isAdmin')->getData()){
                $roles[] = 'ROLE_ADMIN';
            }

            $user->setRoles($roles);

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Votre utilisateur a bien été ajouté');
            return $this->redirectToRoute('admin.user.index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param User $user
     * @param Request $request
     * @return Response
     * Delete user from list
     */
    #[Route('/admin/utilisateurs/del/{id}', name: 'admin.user.delete', methods: "GET|POST|DELETE")]
    public function delete(User $user, Request $request, OrderRepository $orderRepository, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        if($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))){
            $userOrders = $orderRepository->findByUser($user, false);
            $anonym = $userRepository->findByUsername('anonym');
            if(!empty($anonym)){
                $anonym = $anonym[0];
            }else{
                $anonym = new User();

                $password = $userPasswordHasherInterface->hashPassword(
                    $anonym,
                    'anonym123aze'
                );

                $anonym->setUsername('anonym');
                $anonym->setEmail('anonym@base.com');
                $anonym->setPassword($password);

            }

            foreach ($userOrders as $userOrder){
                $userOrder->setUser($anonym);
            }

            $this->em->remove($user);
            $this->em->persist($anonym);
            $this->em->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
        }
        return $this->redirectToRoute('admin.user.index');
    }


    /**
     * @param User $user
     * @param Request $request
     * @return Response
     * Change user role: if admin change to client and if client, change to admin
     */
    #[Route('/admin/utilisateurs/passadmin/{id}', name: 'admin.user.passadmin', methods: "GET|POST")]
    public function passAdmin(User $user, Request $request): Response
    {
        $roles = $user->getRoles();
        if(in_array('ROLE_ADMIN', $roles)){
            $index = array_search('ROLE_ADMIN', $roles);
            unset($roles[$index]);
        }else{
            $roles[] = 'ROLE_ADMIN';
        }
        $user->setRoles($roles);

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('admin.user.index');
    }
}
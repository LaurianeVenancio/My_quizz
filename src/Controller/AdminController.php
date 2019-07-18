<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Reponse;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EditUserType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\RegistrationFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class AdminController extends AbstractController
{


    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function index()
    {
        $user = $this -> getUser ();

        $users = $this->repository->findAll();
        return $this->render('admin/admin.html.twig', compact('users', 'user'));
    }

    /**
     * @Route("/admin/user/create", name="admin.user.create")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles($user->getRoles());
            $this->em->persist($user);
            $this->em->flush();

            $contactFormData = $form->getData();
            dump($contactFormData);

            $message = (new \Swift_Message('You got mail'))
                ->setFrom('lauriane.epitech@gmail.com')
                ->setTo($contactFormData->getEmail())
                ->setBody(
                    "Merci de valider votre compte en cliquant sur ce lien http://localhost:8000/user/validate/" . $contactFormData->getID()
                );
            
            $mailer->send($message);
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_create.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin.user.edit", methods="GET|POST")
     * @param User $user
     */
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin.user.delete", methods="DELETE")
     * @param User $user
     */
    public function delete(User $user, Request $request)
    {
        if($this->isCsrfTokenValid('delete' . $user->getId(), $request->get("_token"))){
            $this->em->remove($user);
            $this->em->flush();
            
            return $this->redirectToRoute('admin_users');
        }
        return $this->redirectToRoute('admin_users');

    }
}
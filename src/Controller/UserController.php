<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use Doctrine\Common\Persistence\ObjectManager;




/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    public function __construct(UserRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param User $user
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer): Response
    {
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $id = $this->getUser()->getId();

            $valide = $entityManager->getRepository(User::class)
                ->find($id);

            if (!$valide) {
                throw $this->createNotFoundException(
                'There are no user with the following id: ' . $id
                );
            }
            $valide->setValidated(false);
            $this->em->flush();
            $contactFormData = $form->getData();

            $message = (new \Swift_Message('You got mail'))
                ->setFrom('lauriane.epitech@gmail.com')
                ->setTo($contactFormData->getEmail())
                ->setBody(
                    "Merci de valider votre compte en cliquant sur ce lien http://localhost:8000/user/validate/" . $contactFormData->getID()
                );
            
            $mailer->send($message);
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/validate/{id}", name="validate.update")
     */
    public function updateValidate($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $valide = $entityManager->getRepository(User::class)
            ->find($id);

        if (!$valide) {
            throw $this->createNotFoundException(
            'There are no user with the following id: ' . $id
            );
        }
        $valide->setValidated(true);
        $entityManager->flush();
        return $this->render('/validate.html.twig', compact('valide'));
    }
}

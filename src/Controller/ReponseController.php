<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\User;
use App\Form\ReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ReponseController extends AbstractController
{
    /**
     * @Route("/admin/reponse/", name="reponse_index", methods={"GET"})
     */
    public function index(): Response
    {
        $user = $this -> getUser ();

        $reponses = $this->getDoctrine()
            ->getRepository(Reponse::class)
            ->findAll();

        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/reponse/new", name="reponse_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this -> getUser ();

        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('reponse_index');
        }

        return $this->render('reponse/new.html.twig', [
            'reponse' => $reponse,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/reponse/{id}", name="reponse_show", methods={"GET"})
     */
    public function show(Reponse $reponse): Response
    {
        $user = $this -> getUser ();

        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/reponse/{id}/edit", name="reponse_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reponse $reponse): Response
    {
        $user = $this -> getUser ();

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reponse_index', [
                'id' => $reponse->getId(),
            ]);
        }

        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/reponse/{id}", name="reponse_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reponse $reponse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reponse_index');
    }
}

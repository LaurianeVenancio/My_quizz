<?php

namespace App\Controller;

use App\Entity\Quizz;
use App\Entity\User;
use App\Form\QuizzType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class QuizzController extends AbstractController
{
    /**
     * @Route("/admin/quizz/", name="quizz_index", methods={"GET"})
     */
    public function index(): Response
    {
        $user = $this -> getUser ();

        $quizzs = $this->getDoctrine()
            ->getRepository(Quizz::class)
            ->findAll();

        return $this->render('quizz/index.html.twig', [
            'quizzs' => $quizzs,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/quizz/new", name="quizz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this -> getUser ();

        $quizz = new Quizz();
        $form = $this->createForm(QuizzType::class, $quizz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quizz);
            $entityManager->flush();

            return $this->redirectToRoute('quizz_index');
        }

        return $this->render('quizz/new.html.twig', [
            'quizz' => $quizz,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/quizz//{id}", name="quizz_show", methods={"GET"})
     */
    public function show(Quizz $quizz): Response
    {
        $user = $this -> getUser ();

        return $this->render('quizz/show.html.twig', [
            'quizz' => $quizz,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/quizz//{id}/edit", name="quizz_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quizz $quizz): Response
    {
        $user = $this -> getUser ();

        $form = $this->createForm(QuizzType::class, $quizz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quizz_index', [
                'id' => $quizz->getId(),
            ]);
        }

        return $this->render('quizz/edit.html.twig', [
            'quizz' => $quizz,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/quizz//{id}", name="quizz_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quizz $quizz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quizz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quizz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quizz_index');
    }
}

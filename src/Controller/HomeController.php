<?php

namespace App\Controller;

use App\Entity\Home;
use App\Entity\User;
use App\Entity\Quizz;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Reponse as ReponseEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Reponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



/**
 * @var HomeRepository
 * @Route("/home")
 */
class HomeController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("", name="index_home")
     */
    public function home()
    {
        $user = $this -> getUser ();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('home/home.html.twig', [
            'categories' => $categories,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}", name="quizz_category", methods={"GET"})
     */
    public function show($id)
    {
        $user = $this -> getUser ();

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
    
        $quizzs = $category->getQuizzs();

        return $this->render('home/show.html.twig', [
            'quizzs' => $quizzs,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/question/{id_quizz}", name="quizz_question", methods={"GET", "POST"})
     */
    public function show_question(Request $request, $id_quizz)
    {
        $user = $this -> getUser ();

        $quizz = $this->getDoctrine()
            ->getRepository(Quizz::class)
            ->find($id_quizz);
        $questions = $quizz->getQuestions();
        $data = ["caca" => 'pipi'];

        if ($this->session->has('count')) {
            $countNow = $this->session->get('count');
        }
        else{
            $this->session->set('count', 0);
        }
        $countNow = $this->session->get('count');
        $question = $questions[$countNow];
        //dd($question);
        $reponses = $question->getReponses();
        $form = $this->createFormBuilder($data)
            ->add(
                'reponses', ChoiceType::class, [
                    'choices' => $reponses,
                    'expanded' => true,
                    'multiple' => false,
                    'choice_label' => 'reponse',
                    'choice_value' => 'id',
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Question Suivante'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $countNow++;
            $this->session->set('count', $countNow);
        }


        return $this->render('home/show_question.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
            'reponses' => $reponses,
            'user' => $user,
        ]);
    }


}
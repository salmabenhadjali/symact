<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/")
     * @return Response
     */
    public function homepage()
    {
        return $this->render('question/homepage.html.twig');
    }

    /**
     * @Route("/questions/{slug}")
     * @return Response
     */
    public function show($slug)
    {
        $answers = [
            'answer 1',
            'answer 2',
            'answer 3',
        ];

        dump($slug, $this);

        return $this->render('question/show.html.twig', [
                'question' => str_replace('-', ' ', ucwords($slug)),
                'answers' => $answers,
            ]
        );
    }
}
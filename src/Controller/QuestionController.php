<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController
{
    /**
     * @Route("/homepage")
     * @return Response
     */
    public function homepage()
    {
        return new Response('sss');
    }

    /**
     * @Route("/questions/{slug}")
     * @return Response
     */
    public function show($slug)
    {
        return new Response(
            sprintf(
                'The future page to answer teh question "%s"',
                str_replace('-', ' ', ucwords($slug))
            )
        );
    }
}
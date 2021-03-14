<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SurveyController extends AbstractController
{
    #[Route('/survey', name: 'survey')]
    public function index(): Response
    {
        return $this->render('survey/index.html.twig', [
            'controller_name' => 'SurveyController',
        ]);
    }

	#[Route('/survey/create', name: 'survey_create')]
	public function create(): Response
	{
		return $this->render('survey/create.html.twig', [
			'controller_name' => 'SurveyController',
		]);
	}
}

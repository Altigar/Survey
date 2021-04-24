<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
	#[Route('/stats/{survey}', name: 'stats')]
	public function index(int $survey): Response
	{
		$entityManager =  $this->getDoctrine()->getManager();
		$repository = $entityManager->getRepository(Answer::class);
		$noteStats = $repository->findNoteStatsBySurvey($survey);
		$choiceStats = $repository->findChoiceStatsBySurvey($survey);
		$questionRepository = $entityManager->getRepository(Question::class);
		$questions = $questionRepository->findByIdPersonWithAnswers($survey, $this->getUser()->getId());
		return $this->render('result/index.html.twig', [
			'questions' => $questions,
			'noteStats' => $noteStats,
			'choiceStats' => $choiceStats,
		]);
	}

    #[Route('/stats/{id}/person/{person}', name: 'stats_person')]
    public function person(int $id, int $person): Response
    {
    	$entityManager =  $this->getDoctrine()->getManager();
    	$repository = $entityManager->getRepository(Question::class);

        return $this->render('result/person.html.twig', [
        	'questions' => $repository->findByIdPersonWithAnswers($id, $person),
        ]);
    }
}

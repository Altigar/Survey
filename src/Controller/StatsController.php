<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Pass;
use App\Entity\Question;
use App\Entity\Survey;
use App\Utils\ObjectUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {}

	#[Route('/stats/{survey}', name: 'stats', requirements: ['survey' => '\d+'], methods: ['GET'])]
	public function index(Survey $survey): Response
	{
		$answerRepository = $this->entityManager->getRepository(Answer::class);
		return $this->render('stats/index.html.twig', [
			'title' => 'Stats',
			'survey' => $survey,
			'questions' => $this->entityManager->getRepository(Question::class)->findBySurveyWithOptions($survey),
			'noteStats' => $answerRepository->findNoteStatsBySurvey($survey),
			'choiceStats' => $answerRepository->findChoiceStatsBySurvey($survey),
			'scaleStats' => $answerRepository->findScaleStatsBySurvey($survey)
		]);
	}

	#[Route('/stats/{survey}/people', name: 'stats_people', requirements: ['survey' => '\d+'], methods: ['GET'])]
	public function list(Survey $survey): Response
	{
		return $this->render('stats/list.html.twig', [
			'title' => 'Person list',
			'survey' => $survey,
			'passes' => $this->entityManager->getRepository(Pass::class)->findBy(['survey' => $survey]),
		]);
	}

    #[Route('/stats/{pass}/person', name: 'stats_person', requirements: ['pass' => '\d+'], methods: ['GET'])]
    public function person(Pass $pass): Response
    {
        return $this->render('stats/person.html.twig', [
        	'title' => "Pass #{$pass->getId()}",
        	'questions' => $this->entityManager->getRepository(Question::class)->findByPassWithOptionsAndAnswers($pass),
	        'survey' => $pass->getSurvey()
        ]);
    }
}

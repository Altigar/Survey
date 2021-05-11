<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Pass;
use App\Entity\Question;
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

	#[Route('/stats/{survey}', name: 'stats')]
	public function index(int $survey): Response
	{
		$repository = $this->entityManager->getRepository(Answer::class);
		$noteStats = $repository->findNoteStatsBySurvey($survey);
		$choiceStats = $repository->findChoiceStatsBySurvey($survey);
		$questionRepository = $this->entityManager->getRepository(Question::class);
		$questions = $questionRepository->findByIdPersonWithAnswers($survey, $this->getUser()->getId());
		return $this->render('stats/index.html.twig', [
			'questions' => $questions,
			'noteStats' => $noteStats,
			'choiceStats' => $choiceStats,
			'survey' => $survey,
		]);
	}

	#[Route('/stats/{survey}/person/list', name: 'stats_person_list')]
	public function list(int $survey): Response
	{
		$repository = $this->entityManager->getRepository(Pass::class);
		return $this->render('stats/list.html.twig', [
			'title' => 'Person list',
			'survey' => $survey,
			'passes' => $repository->findBy(['survey' => $survey]),
		]);
	}

    #[Route('/stats/{survey}/person/{person}', name: 'stats_person')]
    public function person(int $survey, int $person): Response
    {
    	$repository = $this->entityManager->getRepository(Question::class);
    	$questions = $repository->findBy(['survey' => $survey]);

    	$answerRepository = $this->entityManager->getRepository(Answer::class);
    	$answers = $answerRepository->findBy(['question' => ObjectUtil::getColumn($questions, 'id'), 'person' => $person]);
        return $this->render('stats/person.html.twig', [
        	'questions' => $questions,
	        'answers' => ObjectUtil::reindexRelation($answers, 'option'),
        ]);
    }
}

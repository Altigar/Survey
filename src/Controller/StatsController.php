<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Pass;
use App\Entity\Question;
use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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

	#[Route('/stats/{survey}/pass/list', name: 'stats_pass_list', requirements: ['survey' => '\d+'], methods: ['GET'])]
	public function list(Request $request, Survey $survey): Response
	{
		$passes = $this->entityManager->getRepository(Pass::class)->findBy(['survey' => $survey], ['id' => 'DESC']);
		if ($request->isXmlHttpRequest()) {
			return $this->json($passes, context: [AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey', 'externalPerson', 'person']]);
		}
		return $this->render('stats/list.html.twig', [
			'title' => 'Pass list',
			'survey' => $survey,
			'passes' => $passes
		]);
	}

    #[Route('/stats/pass/{pass}', name: 'stats_pass_view', requirements: ['pass' => '\d+'], methods: ['GET'])]
    public function view(Pass $pass): Response
    {
        return $this->render('stats/person.html.twig', [
        	'title' => "Pass #{$pass->getId()}",
        	'questions' => $this->entityManager->getRepository(Question::class)->findByPassWithOptionsAndAnswers($pass),
	        'survey' => $pass->getSurvey()
        ]);
    }

	#[Route('/stats/pass/{pass}', name: 'stats_pass_delete', requirements: ['pass' => '\d+'], methods: ['DELETE'])]
	public function delete(Pass $pass): JsonResponse
	{
		$this->entityManager->remove($pass);
		$this->entityManager->flush();

		return $this->json([], Response::HTTP_NO_CONTENT);
	}
}

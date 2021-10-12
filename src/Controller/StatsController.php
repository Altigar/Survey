<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Survey;
use App\Repository\AnswerRepository;
use App\Repository\PassRepository;
use App\Repository\QuestionRepository;
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
		private EntityManagerInterface $entityManager,
		private QuestionRepository $questionRepository,
		private AnswerRepository $answerRepository,
		private PassRepository $passRepository,
	) {}

	#[Route('/stats/{survey}', name: 'stats', requirements: ['survey' => '\d+'], methods: ['GET'])]
	public function index(Survey $survey): Response
	{
		return $this->render('stats/index.html.twig', [
			'title' => 'Stats',
			'survey' => $survey,
			'questions' => $this->questionRepository->findBySurveyWithOptions($survey),
			'noteStats' => $this->answerRepository->findNoteStatsBySurvey($survey),
			'choiceStats' => $this->answerRepository->findChoiceStatsBySurvey($survey),
			'scaleStats' => $this->answerRepository->findScaleStatsBySurvey($survey)
		]);
	}

	#[Route('/stats/{survey}/pass/list', name: 'stats_pass_list', requirements: ['survey' => '\d+'], methods: ['GET'])]
	public function list(Request $request, Survey $survey): Response
	{
		$passes = $this->passRepository->findBy(['survey' => $survey], ['id' => 'DESC']);
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
        	'questions' => $this->questionRepository->findByPassWithOptionsAndAnswers($pass),
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

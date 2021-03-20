<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Survey;
use App\Services\EntityMapper;
use App\Services\QuestionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SurveyController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
	) {}

    #[Route('/survey', name: 'survey')]
    public function index(): Response
    {
        return $this->render('survey/index.html.twig', [
            'controller_name' => 'SurveyController',
        ]);
    }

	#[Route('/survey/create', name: 'survey_create')]
	public function create(Request $request): Response
	{
		if ($request->isMethod('post')) {
			$survey = (new Survey())->setCreatedAt(new DateTime('now'));
			$this->entityManager->persist($survey);
			$this->entityManager->flush();

			return $this->redirectToRoute('survey_plan', ['id' => $survey->getId()]);
		}

		return $this->render('survey/create.html.twig', [
			'controller_name' => 'SurveyController',
		]);
	}

	#[Route('/survey/plan/{id}', name: 'survey_plan')]
	public function plan(Request $request, EntityMapper $entityMapper, QuestionService $questionService): Response|JsonResponse
	{
		if ($request->isMethod('post')) {
			$entityMapper->validate($request->getContent(), Question::class);
			if ($errors = $entityMapper->getErrors(Question::class)) {
				return new JsonResponse($errors, 400);
			}
			$rawData = $entityMapper->getRawData(Question::class);
			if ($rawData['id'] ?? null) {
				$questionService->update($rawData);
			} else {
				/** @var $question Question */
				$question = $entityMapper->getEntity(Question::class);
				$questionService->create($question, $request->attributes->get('id'));
			}
		}

		$repository = $this->entityManager->getRepository(Question::class);
		$questions = $repository->findBy(['survey' => $request->attributes->get('id')]);
		$json = $this->serializer->serialize($questions, 'json');

		return $this->render('survey/plan.html.twig', [
			'controller_name' => 'SurveyController',
			'id' => $request->attributes->get('id'),
			'json' => $json,
		]);
	}
}

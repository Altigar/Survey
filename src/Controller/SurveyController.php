<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Survey;
use App\Services\QuestionService;
use App\Services\ValidationService;
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
			$survey = (new Survey())->setCreatedAt(new \DateTime('now'));
			$this->entityManager->persist($survey);
			$this->entityManager->flush();

			return $this->redirectToRoute('survey_plan', ['id' => $survey->getId()]);
		}

		return $this->render('survey/create.html.twig', [
			'controller_name' => 'SurveyController',
		]);
	}

	#[Route('/survey/plan/{id}', name: 'survey_plan', methods: ['GET'])]
	public function plan(int $id): Response
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$questions = $repository->findBy(['survey' => $id]);
		$json = $this->serializer->serialize($questions, 'json');

		return $this->render('survey/plan.html.twig', [
			'controller_name' => 'SurveyController',
			'id' => $id,
			'json' => $json,
		]);
	}

	#[Route('/survey/plan/{id}/all', name: 'survey_plan_all', methods: ['GET'])]
	public function all(int $id): JsonResponse
	{
		$repository = $this->entityManager->getRepository(Question::class);
		$questions = $repository->findBy(['survey' => $id]);
		return $questions ?
			new JsonResponse($this->serializer->serialize($questions, 'json'), JsonResponse::HTTP_OK) :
			new JsonResponse(['text' => 'Questions not found'], JsonResponse::HTTP_NOT_FOUND);
	}

	#[Route('/survey/plan/{id}/add', name: 'survey_plan_add', methods: ['POST'])]
	public function add(int $id, Request $request, QuestionService $questionService): JsonResponse
	{
		$data = $this->serializer->decode($request->getContent(), 'json');
		return $questionService->create($id, $data) ?
			new JsonResponse([], JsonResponse::HTTP_OK) :
			new JsonResponse(['text' => 'Failed to add question'], JsonResponse::HTTP_NOT_FOUND);
	}

	#[Route('/survey/plan/{id}/update', name: 'survey_plan_update', methods: ['PUT'])]
	public function update(Request $request, QuestionService $questionService, ValidationService $validationService): JsonResponse
	{
		$question = $this->serializer->deserialize($request->getContent(), Question::class, 'json');
		$validationService->validate($question);
		if ($errors = $validationService->getErrors(Question::class)) {
			return new JsonResponse($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
		}
		$data = $this->serializer->decode($request->getContent(), 'json');
		return $questionService->update($data) ?
			new JsonResponse([], JsonResponse::HTTP_OK) :
			new JsonResponse(['text' => 'Failed to update question'], JsonResponse::HTTP_NOT_FOUND);
	}

	#[Route('/survey/plan/{id}/remove', name: 'survey_plan_remove', methods: ['DELETE'])]
	public function remove(Request $request, QuestionService $questionService): JsonResponse
	{
		$data = $this->serializer->decode($request->getContent(), 'json');
		return $questionService->delete($data) ?
			new JsonResponse([], JsonResponse::HTTP_OK) :
			new JsonResponse(['text' => 'Failed to remove question'], JsonResponse::HTTP_NOT_FOUND);
	}
}

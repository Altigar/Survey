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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ContentController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private PropertyAccessorInterface $accessor,
	) {}

    #[Route('/content/{survey}', name: 'content', methods: ['GET'])]
    public function index(int $survey, QuestionService $questionService): Response
    {
	    $surveyRepository = $this->entityManager->getRepository(Survey::class);
	    if (!$surveyRepository->find($survey)) {
		    throw new NotFoundHttpException();
	    }
	    $questions = $questionService->getBySurvey($survey);
	    return $this->render('content/index.html.twig', [
		    'title' => 'Content',
		    'survey' => $survey,
		    'questions' => $this->serializer->serialize($questions, 'json', [
			    AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']
		    ]),
		    'options' => $this->serializer->serialize([
			    ['value' => 'radio', 'text' => 'radio'],
			    ['value' => 'checkbox', 'text' => 'checkbox'],
			    ['value' => 'string', 'text' => 'string'],
			    ['value' => 'text', 'text' => 'text'],
		    ],'json'),
	    ]);
    }

	#[Route('/content/{survey}/create', name: 'content_create', methods: ['POST'])]
	public function create(int $survey, Request $request, QuestionService $questionService): JsonResponse
	{
		$data = $this->serializer->decode($request->getContent(), 'json');
		$created = $this->accessor->getValue($data, '[type]') ? $questionService->create($survey, $data) : false;
		if ($created) {
			return $this->json($questionService->getBySurvey($survey), JsonResponse::HTTP_OK, context: [
				AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers']
			]);
		} else {
			return $this->json(['text' => 'Failed to add question'], JsonResponse::HTTP_NOT_FOUND);
		}
	}

	#[Route('/content/{survey}/update', name: 'content_update', methods: ['PUT'])]
	public function update(Request $request, QuestionService $questionService, ValidationService $validationService): JsonResponse
	{
		$question = $this->serializer->deserialize($request->getContent(), Question::class, 'json');
		$validationService->validate($question);
		if ($errors = $validationService->getErrors(Question::class)) {
			return $this->json($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
		}
		$data = $this->serializer->decode($request->getContent(), 'json');
		match ($this->accessor->getValue($data, '[type]')) {
			'radio', 'checkbox' => $updated = $questionService->updateChoice($data),
			'string', 'text' => $updated = $questionService->updateNote($data),
			default => $updated = false,
		};
		if ($updated) {
			return $this->json([], JsonResponse::HTTP_OK);
		} else {
			return $this->json(['text' => 'Failed to update question'], JsonResponse::HTTP_NOT_FOUND);
		}
	}

	#[Route('/content/{survey}/remove', name: 'content_remove', methods: ['DELETE'])]
	public function remove(int $survey, Request $request, QuestionService $questionService): JsonResponse
	{
		$data = $this->serializer->decode($request->getContent(), 'json');
		if ($questionService->delete($data)) {
			return $this->json($questionService->getBySurvey($survey), JsonResponse::HTTP_OK, context: [
				AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers']
			]);
		} else {
			return $this->json(['text' => 'Failed to remove question'], JsonResponse::HTTP_NOT_FOUND);
		}
	}
}

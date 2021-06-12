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
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ContentController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private PropertyAccessorInterface $accessor,
	) {}

    #[Route('/content/{survey}', name: 'content',  requirements: ['survey' => '\d+'], methods: ['GET'])]
    public function index(Request $request, Survey $survey): Response
    {
	    $questions = $this->entityManager->getRepository(Question::class)->findBy(['survey' => $survey], ['ordering' => 'asc']);
	    if ($request->isXmlHttpRequest()) {
	    	return $this->json($questions);
	    } else {
		    return $this->render('content/index.html.twig', [
			    'title' => 'Content',
			    'survey' => $survey->getId(),
			    'questions' => $this->serializer->serialize($questions, 'json', [
				    AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']
			    ]),
			    'options' => $this->serializer->serialize([
				    ['value' => 'radio', 'text' => 'radio'],
				    ['value' => 'checkbox', 'text' => 'checkbox'],
				    ['value' => 'string', 'text' => 'string'],
				    ['value' => 'text', 'text' => 'text'],
				    ['value' => 'scale', 'text' => 'scale'],
			    ], 'json'),
		    ]);
	    }
    }

	#[Route('/content/{survey}', name: 'content_create', requirements: ['survey' => '\d+'], methods: ['POST'])]
	public function create(Survey $survey, Request $request, QuestionService $questionService, ValidationService $validationService): JsonResponse
	{
		$question = $this->serializer->deserialize($request->getContent(), Question::class,'json', [
			AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true
		]);
		if ($errors = $validationService->validate($question, ['default'])) {
			return $this->json($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
		}
		$questionService->create($survey, $question);
		return $this->json([], JsonResponse::HTTP_CREATED);
	}

	#[Route('/content/{survey}', name: 'content_update', methods: ['PUT'])]
	public function update(Request $request, QuestionService $questionService, ValidationService $validationService): JsonResponse
	{
		$question = $this->serializer->deserialize($request->getContent(), Question::class, 'json');
		in_array($question->getType(), ['radio', 'checkbox']) ? $group = 'choice' : $group = null;
		if ($errors = $validationService->validate($question, [$group])) {
			return $this->json($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
		}
		match ($question->getType()) {
			'radio', 'checkbox' => $updated = $questionService->updateChoice($question),
			'string', 'text' => $updated = $questionService->updateNote($question),
			'scale' => $updated = $questionService->updateScale($question),
			default => $updated = false,
		};
		if ($updated) {
			return $this->json([], JsonResponse::HTTP_OK);
		} else {
			return $this->json(['text' => 'Failed to update question'], JsonResponse::HTTP_NOT_FOUND);
		}
	}

	#[Route('/content/{survey}', name: 'content_remove', methods: ['DELETE'])]
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

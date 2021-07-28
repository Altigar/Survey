<?php

namespace App\Controller;

use App\Data\Content\Create\QuestionData;
use App\Data\Content\Update\QuestionData as QuestionDataUpdate;
use App\Entity\Question;
use App\Entity\Survey;
use App\Services\QuestionService;
use App\Services\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ContentController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private QuestionService $questionService,
		private ValidationService $validationService,
	) {}

    #[Route('/content/{survey}', name: 'content',  requirements: ['survey' => '\d+'], methods: ['GET'])]
    public function index(Request $request, Survey $survey): Response
    {
	    $questions = $this->entityManager->getRepository(Question::class)->findBy(['survey' => $survey], ['ordering' => 'asc']);
	    if ($request->isXmlHttpRequest()) {
	    	return $this->json($questions, context: [AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']]);
	    }
	    return $this->render('content/index.html.twig', [
		    'title' => 'Content',
		    'survey' => $survey->getId(),
		    'questions' => $this->serializer->serialize($questions, 'json', [
			    AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']
		    ]),
	    ]);
    }

	#[Route('/content/{survey}', name: 'content_create', requirements: ['survey' => '\d+'], methods: ['POST'])]
	public function create(Request $request, Survey $survey): JsonResponse
	{
		try {
			$questionData = $this->serializer->deserialize($request->getContent(), QuestionData::class,'json');
			if ($errors = $this->validationService->validate($questionData, ['default'])) {
				return $this->json($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
			}
			return $this->json([
				'code' => JsonResponse::HTTP_CREATED,
				'status' => 'OK',
				'data' => ['id' => $this->questionService->create($survey, $questionData)]
			], JsonResponse::HTTP_CREATED);
		} catch (\Throwable) {
			throw new BadRequestException();
		}
	}

	#[Route('/content/{question}', name: 'content_update', requirements: ['question' => '\d+'], methods: ['PUT'])]
	public function update(Request $request, Question $question): JsonResponse
	{
		$questionData = $this->serializer->deserialize($request->getContent(), QuestionDataUpdate::class, 'json');
		$group = (array)match ($type = $questionData->getType()) {
			Question::TYPE_RADIO, Question::TYPE_CHECKBOX => 'choice',
			Question::TYPE_TEXT => 'text',
			default => [],
		};
		if ($errors = $this->validationService->validate($questionData, array_merge($group, ['default']))) {
			return $this->json($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
		}
		match ($type) {
			Question::TYPE_RADIO, Question::TYPE_CHECKBOX => $this->questionService->updateChoice($question, $questionData),
			Question::TYPE_STRING => $this->questionService->updateString($question, $questionData),
			Question::TYPE_TEXT => $this->questionService->updateText($question, $questionData),
			Question::TYPE_SCALE => $this->questionService->updateScale($question, $questionData),
		};
		return $this->json([]);
	}

	#[Route('/content/{question}', name: 'content_remove', requirements: ['question' => '\d+'], methods: ['DELETE'])]
	public function remove(Question $question): JsonResponse
	{
		$this->entityManager->remove($question);
		$this->entityManager->flush();
		return $this->json([], JsonResponse::HTTP_NO_CONTENT);
	}
}

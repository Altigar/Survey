<?php

namespace App\Controller;

use App\Data\Content\QuestionDataCreate;
use App\Data\Content\QuestionDataUpdate;
use App\Entity\Question;
use App\Entity\Survey;
use App\Exception\Content\UpdateValidationException;
use App\Exception\ValidationException;
use App\Services\QuestionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContentController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private QuestionService $questionService,
		private ValidatorInterface $validator,
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
		$questionData = $this->serializer->deserialize($request->getContent(), QuestionDataCreate::class,'json');
		$errors = $this->validator->validate($questionData, groups: ['default']);
		if ($errors->count()) {
			throw new ValidationException($errors);
		}
		return $this->json(['id' => $this->questionService->create($survey, $questionData)], Response::HTTP_CREATED);
	}

	#[Route('/content/{question}', name: 'content_update', requirements: ['question' => '\d+'], methods: ['PUT'])]
	public function update(Request $request, Question $question): JsonResponse
	{
		$questionData = $this->serializer->deserialize($request->getContent(), QuestionDataUpdate::class, 'json');
		$errors = $this->validator->validate($questionData, groups: [$questionData->getType(), 'default']);
		if ($errors->count()) {
			throw new UpdateValidationException($errors);
		}
		$this->questionService->update($question, $questionData);
		return $this->json([]);
	}

	#[Route('/content/{question}', name: 'content_remove', requirements: ['question' => '\d+'], methods: ['DELETE'])]
	public function remove(Question $question): JsonResponse
	{
		$this->entityManager->remove($question);
		$this->entityManager->flush();
		return $this->json([], Response::HTTP_NO_CONTENT);
	}
}

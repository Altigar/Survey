<?php

namespace App\Controller;

use App\Data\Content\QuestionDataCreate;
use App\Data\Content\QuestionDataUpdate;
use App\Entity\Question;
use App\Entity\Survey;
use App\Exception\Content\UpdateValidationException;
use App\Exception\ValidationException;
use App\Repository\QuestionRepository;
use App\Security\ContentVoter;
use App\Services\QuestionService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContentController extends AbstractController implements CsrfTokenControllerInterface
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private QuestionService $questionService,
		private ValidatorInterface $validator,
		private QuestionRepository $questionRepository,
	) {}

    #[Route('/content/{survey}', name: 'content',  requirements: ['survey' => '\d+'], methods: ['GET'])]
    #[IsGranted(ContentVoter::VIEW, 'survey')]
    public function index(Request $request, Survey $survey): Response
    {
	    $questions = $this->questionRepository->findBySurveyWithOptions($survey);
	    if ($request->isXmlHttpRequest()) {
	    	return $this->json($questions, context: [AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']]);
	    }
	    return $this->render('content/index.html.twig', [
		    'title' => 'Content',
		    'survey' => $survey,
		    'questions' => $questions
	    ]);
    }

	#[Route('/content/{survey}', name: 'content_create', requirements: ['survey' => '\d+'], methods: ['POST'])]
	#[IsGranted(ContentVoter::CREATE, 'survey')]
	public function create(Request $request, Survey $survey): JsonResponse
	{
		$questionData = $this->serializer->deserialize($request->getContent(), QuestionDataCreate::class,'json');
		$errors = $this->validator->validate($questionData, groups: ['default']);
		if ($errors->count()) {
			throw new ValidationException($errors);
		}

		$question = $this->questionService->create($survey, $questionData);
		$this->entityManager->persist($question);
		$this->entityManager->flush();

		return $this->json(['id' => $question->getId()], Response::HTTP_CREATED);
	}

	#[Route('/content/{question}', name: 'content_update', requirements: ['question' => '\d+'], methods: ['PUT'])]
	#[IsGranted(ContentVoter::UPDATE, 'question')]
	public function update(Request $request, Question $question): JsonResponse
	{
		$questionData = $this->serializer->deserialize($request->getContent(), QuestionDataUpdate::class, 'json');
		$errors = $this->validator->validate($questionData, groups: [$questionData->getType(), 'default']);
		if ($errors->count()) {
			throw new UpdateValidationException($errors);
		}
		$this->questionService->update($question, $questionData);
		$this->entityManager->flush();

		return $this->json([]);
	}

	#[Route('/content/{question}', name: 'content_remove', requirements: ['question' => '\d+'], methods: ['DELETE'])]
	#[IsGranted(ContentVoter::DELETE, 'question')]
	public function remove(Question $question): JsonResponse
	{
		$this->entityManager->remove($question);
		$this->entityManager->flush();

		return $this->json([], Response::HTTP_NO_CONTENT);
	}
}

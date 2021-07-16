<?php

namespace App\Controller;

use App\Data\Pass\QuestionData;
use App\Entity\Question;
use App\Entity\Survey;
use App\Services\AnswerService;
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

class PassController extends AbstractController
{
	public function __construct(
		private SerializerInterface $serializer,
		private ValidationService $validationService,
		private AnswerService $answerService,
		private EntityManagerInterface $entityManager,
	) {}

    #[Route('/pass/{survey}', name: 'pass', methods: ['GET'])]
    public function index(Survey $survey): Response
    {
	    $questions = $this->entityManager->getRepository(Question::class)->findBy(['survey' => $survey]);
        return $this->render('pass/index.html.twig', [
        	'title' => 'Survey',
        	'id' => $survey->getId(),
        	'questions' => $this->serializer->serialize($questions, 'json', [
        		AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']
	        ]),
        ]);
    }

	#[Route('/pass/{survey}', name: 'pass_create', methods: ['POST'])]
	public function create(Request $request, Survey $survey): JsonResponse
	{
		try {
			$data = $this->serializer->deserialize($request->getContent(), QuestionData::class . '[]', 'json');
		} catch (\Exception $e) {
			throw new BadRequestException();
		}
		if ($errors = $this->validationService->validatePass($data)) {
			return $this->json([
				'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
				'errors' => $errors
			], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
		}
		$this->answerService->create($data, $survey, $this->getUser());
		return $this->json([], JsonResponse::HTTP_CREATED);
	}
}

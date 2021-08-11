<?php

namespace App\Controller;

use App\Data\Pass\QuestionData;
use App\Entity\Question;
use App\Entity\Survey;
use App\Exception\Pass\CreateValidationException;
use App\Services\AnswerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PassController extends AbstractController
{
	public function __construct(
		private SerializerInterface $serializer,
		private ValidatorInterface $validator,
		private AnswerService $answerService,
		private EntityManagerInterface $entityManager,
	) {}

    #[Route('/pass/{survey}', name: 'pass', methods: ['GET'])]
    public function index(Survey $survey): Response
    {
	    $questions = $this->entityManager->getRepository(Question::class)->findBy(['survey' => $survey]);
        return $this->render('pass/index.html.twig', [
        	'title' => 'Survey',
        	'survey' => $survey,
        	'questions' => $this->serializer->serialize($questions, 'json', [
        		AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']
	        ]),
        ]);
    }

	#[Route('/pass/{survey}', name: 'pass_create', methods: ['POST'])]
	public function create(Request $request, Survey $survey): JsonResponse
	{
		$data = $this->serializer->deserialize($request->getContent(), QuestionData::class . '[]', 'json');
		$errors = new ConstraintViolationList();
		foreach ($data as $questionData) {
			$error = $this->validator->validate($questionData, groups: [$questionData->getType(), 'required']);
			if ($error->count()) {
				$errors->addAll($error);
			}
		}
		if ($errors->count()) {
			throw new CreateValidationException($errors);
		}
		$this->answerService->create($data, $survey, $this->getUser());
		return $this->json([], JsonResponse::HTTP_CREATED);
	}
}

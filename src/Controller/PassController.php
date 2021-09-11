<?php

namespace App\Controller;

use App\Data\Pass\QuestionData;
use App\Entity\Survey;
use App\Exception\Pass\CreateValidationException;
use App\Repository\PassRepository;
use App\Repository\QuestionRepository;
use App\Services\AnswerService;
use App\Services\PassService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PassController extends AbstractController implements CsrfTokenControllerInterface
{
	public function __construct(
		private SerializerInterface $serializer,
		private ValidatorInterface $validator,
		private AnswerService $answerService,
		private PassService $passService,
		private QuestionRepository $questionRepository,
		private PassRepository $passRepository,
	) {}

    #[Route('/pass/{hash}', name: 'pass', requirements: ['hash' => '[0-9a-zA-Z]+'], methods: ['GET'])]
    public function index(Request $request, Survey $survey): Response
    {
	    return $this->render('pass/index.html.twig', [
        	'title' => 'Survey',
        	'survey' => $survey,
		    'questions' => $this->questionRepository->findBySurveyWithOptions($survey),
        	'pass' => $this->passRepository->findByPersonOrIp($survey, $this->getUser(), $request->getClientIp())
        ]);
    }

	#[Route('/pass/{hash}', name: 'pass_create', requirements: ['hash' => '[0-9a-zA-Z]+'], methods: ['POST'])]
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
		$pass = $this->passService->create($survey, $this->getUser(), $request->getClientIp());
		$this->answerService->create($data, $survey, $pass);
		return $this->json(null, Response::HTTP_CREATED);
	}
}

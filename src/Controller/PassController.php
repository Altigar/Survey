<?php

namespace App\Controller;

use App\Data\Pass\QuestionData;
use App\Entity\ExternalPerson;
use App\Entity\Pass;
use App\Entity\Survey;
use App\Exception\Pass\CreateValidationException;
use App\Repository\ExternalPersonRepository;
use App\Repository\PassRepository;
use App\Repository\QuestionRepository;
use App\Services\AnswerService;
use Doctrine\ORM\EntityManagerInterface;
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
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private ValidatorInterface $validator,
		private AnswerService $answerService,
		private QuestionRepository $questionRepository,
		private PassRepository $passRepository,
		private ExternalPersonRepository $externalPersonRepository,
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
		if ($person = $this->getUser()) {
			$externalPerson = null;
		} else {
			$externalPerson = $this->externalPersonRepository->findOneBy(['ip' => $request->getClientIp()]);
		}
		if (!$person && !$externalPerson) {
			$externalPerson = ExternalPerson::create($request->getClientIp());
		}
		$questions = $this->questionRepository->findIndexedBySurveyWithIndexedOptions($survey);
		$pass = $this->answerService->create($data, $questions, Pass::create($survey, $person, $externalPerson));

		$this->entityManager->persist($pass);
		$this->entityManager->flush();

		return $this->json(null, Response::HTTP_CREATED);
	}
}

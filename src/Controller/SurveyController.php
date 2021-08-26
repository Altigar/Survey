<?php

namespace App\Controller;

use App\Data\Survey\SurveyData;
use App\Entity\Survey;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SurveyController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private ValidatorInterface $validator,
	) {}

    #[Route('/survey', name: 'survey', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('survey/index.html.twig', [
            'title' => 'Surveys',
	        'surveys' => $this->entityManager->getRepository(Survey::class)->findBy([
	        	'person' => $this->getUser()
	        ])
        ]);
    }

	#[Route('/survey/create', name: 'survey_create', methods: ['GET'])]
	public function create(): Response
	{
		return $this->render('survey/create.html.twig', ['title' => 'Create survey']);
	}

	#[Route('/survey/create', name: 'survey_store', methods: ['POST'])]
	public function store(Request $request): JsonResponse
	{
		if (!$this->isCsrfTokenValid('default', $request->headers->get('X-CSRF-TOKEN'))) {
			return $this->json([], Response::HTTP_FORBIDDEN);
		}
		$surveyData = $this->serializer->deserialize($request->getContent(), SurveyData::class, 'json');
		$errors = $this->validator->validate($surveyData);
		if ($errors->count()) {
			throw new ValidationException($errors);
		}
		$survey = Survey::create(
			$this->getUser(),
			$surveyData->getName(),
			$surveyData->getDescription(),
			$surveyData->getRepeatable()
		);
		$this->entityManager->persist($survey);
		$this->entityManager->flush();

		return $this->json(['id' => $survey->getId()], Response::HTTP_CREATED);
	}

	#[Route('/survey/edit/{survey}', name: 'survey_edit', methods: ['GET'])]
	public function edit(Survey $survey): Response
	{
		return $this->render('survey/edit.html.twig', [
			'title' => 'Edit survey',
			'survey' => $survey,
			'data' => $this->serializer->serialize($survey, 'json', [
				AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'questions', 'passes', 'person']
			]),
		]);
	}

	#[Route('/survey/edit/{survey}', name: 'survey_update', methods: ['PUT'])]
	public function update(Request $request, Survey $survey): JsonResponse
	{
		$surveyData = $this->serializer->deserialize($request->getContent(), SurveyData::class, 'json');
		$errors = $this->validator->validate($surveyData);
		if ($errors->count()) {
			throw new ValidationException($errors);
		}
		$survey->update($surveyData->getName(), $surveyData->getDescription(), $surveyData->getRepeatable());
		$this->entityManager->persist($survey);
		$this->entityManager->flush();

		return $this->json(['id' => $survey->getId()], Response::HTTP_CREATED);
	}
}

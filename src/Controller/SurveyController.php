<?php

namespace App\Controller;

use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Survey;
use App\Services\EntityMapper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SurveyController extends AbstractController
{
	public EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

    #[Route('/survey', name: 'survey')]
    public function index(): Response
    {
        return $this->render('survey/index.html.twig', [
            'controller_name' => 'SurveyController',
        ]);
    }

	#[Route('/survey/create', name: 'survey_create')]
	public function create(Request $request): Response
	{
		if ($request->isMethod('post')) {
			$survey = (new Survey())->setCreatedAt(new DateTime('now'));
			$this->entityManager->persist($survey);
			$this->entityManager->flush();

			return $this->redirectToRoute('survey_plan', ['id' => $survey->getId()]);
		}

		return $this->render('survey/create.html.twig', [
			'controller_name' => 'SurveyController',
		]);
	}

	#[Route('/survey/plan/{id}', name: 'survey_plan')]
	public function plan(Request $request, EntityMapper $entityMapper, SerializerInterface $serializer): Response|JsonResponse
	{
		if ($request->isMethod('post')) {
			$entityMapper->validate($request->getContent(), Question::class);
			if ($errors = $entityMapper->getErrors(Question::class)) {
				return new JsonResponse($errors, 400);
			}
			$repository = $this->entityManager->getRepository(Question::class);
			$rawData = $entityMapper->getRawData(Question::class);
			if ($id = $rawData['id'] ?? null) {
				$question = $repository->findById($id);
				$question->setType($rawData['type'] ?? null);
				$question->setText($rawData['text'] ?? null);
				$options = $question->getOptions()->toArray();
				foreach ($rawData['options'] ?? [] as $rawOption) {
					$optionId = $rawOption['id'] ?? null;
					if ($option = $options[$optionId] ?? null) {
						$option->setText($rawOption['text'] ?? null);
					} else {
						$option = (new Option())
							->setText($rawOption['text'] ?? null)
							->setQuestion($question);
						$question->addOption($option);
					}
				}
			} else {
				$question = $entityMapper->getEntity(Question::class);
				$repository = $this->entityManager->getRepository(Survey::class);
				$survey = $repository->find($request->attributes->get('id'));
				$question->setSurvey($survey);
				$question->setCreatedAt(new DateTime('now'));
			}
			$this->entityManager->persist($question);
			$this->entityManager->flush();
		}

		$repository = $this->entityManager->getRepository(Question::class);
		$questions = $repository->findBy(['survey' => $request->attributes->get('id')]);
		$json = $serializer->serialize($questions, 'json');

		return $this->render('survey/plan.html.twig', [
			'controller_name' => 'SurveyController',
			'id' => $request->attributes->get('id'),
			'json' => $json,
		]);
	}
}

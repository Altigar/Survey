<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Survey;
use App\Services\AnswerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PassController extends AbstractController
{
    #[Route('/pass/{survey}', name: 'pass', methods: ['GET'])]
    public function index(Survey $survey, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
	    $questions = $entityManager->getRepository(Question::class)->findBy(['survey' => $survey]);
        return $this->render('pass/index.html.twig', [
        	'title' => 'Survey',
        	'id' => $survey->getId(),
        	'questions' => $serializer->serialize($questions, 'json', [
        		AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers', 'survey']
	        ]),
        ]);
    }

	#[Route('/pass/{survey}', name: 'pass_create', methods: ['POST'])]
	public function create(Request $request, Survey $survey, SerializerInterface $serializer, AnswerService $service): JsonResponse
	{
		$data = $serializer->deserialize($request->getContent(), Question::class . '[]', 'json');
		$service->create($data, $survey, $this->getUser());
		return $this->json([], JsonResponse::HTTP_CREATED);
	}
}

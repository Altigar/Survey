<?php

namespace App\Controller;

use App\Entity\Question;
use App\Services\AnswerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PassController extends AbstractController
{
    #[Route('/pass/{id}', name: 'pass')]
    public function index(int $id, Request $request, SerializerInterface $serializer): Response
    {
    	$repository = $this->getDoctrine()->getManager()->getRepository(Question::class);
    	$questions = $repository->findBy(['survey' => $id]);
        return $this->render('pass/index.html.twig', [
        	'title' => 'Survey',
        	'id' => $id,
        	'questions' => $serializer->serialize($questions, 'json', [
        		AbstractNormalizer::IGNORED_ATTRIBUTES => ['answers']
	        ]),
        ]);
    }

	#[Route('/pass/{id}/create', name: 'pass_create', methods: ['POST'])]
	public function create(int $id, Request $request, SerializerInterface $serializer, AnswerService $service): JsonResponse
	{
		$data = $serializer->decode($request->getContent(), 'json');
		if ($service->create($data, $id, $this->getUser())) {
			$data = [];
			$code = JsonResponse::HTTP_CREATED;
		} else {
			$data = ['text' => 'Failed to create answer'];
			$code = JsonResponse::HTTP_BAD_REQUEST;
		}
		return $this->json($data, $code);
	}
}

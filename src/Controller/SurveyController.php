<?php

namespace App\Controller;

use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SurveyController extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private SerializerInterface $serializer,
		private PropertyAccessorInterface $accessor,
	) {}

    #[Route('/survey', name: 'survey', methods: ['GET'])]
    public function index(): Response
    {
    	$repository = $this->entityManager->getRepository(Survey::class);
        return $this->render('survey/index.html.twig', [
            'title' => 'Profile',
	        'surveys' => $repository->findBy(['person' => $this->getUser()->getId()])
        ]);
    }

	#[Route('/survey/create', name: 'survey_create', methods: ['GET', 'POST'])]
	public function create(Request $request): Response
	{
		if ($request->isMethod('post')) {
			$survey = Survey::create($this->getUser(), $request->get('name'), $request->get('name'));
			$this->entityManager->persist($survey);
			$this->entityManager->flush();

			return $this->redirectToRoute('content', ['survey' => $survey->getId()]);
		}

		return $this->render('survey/create.html.twig', [
			'title' => 'New survey'
		]);
	}
}

<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    #[Route('/result/{id}/person/{person}', name: 'result')]
    public function index(int $id, int $person): Response
    {
    	$entityManager =  $this->getDoctrine()->getManager();
    	$repository = $entityManager->getRepository(Question::class);

        return $this->render('result/index.html.twig', [
        	'questions' => $repository->findByIdPersonWithAnswers($id, $person),
        ]);
    }
}

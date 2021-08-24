<?php

namespace App\Controller;

use App\Entity\Survey;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShareController extends AbstractController
{
    #[Route('/share/{survey}', name: 'share',  requirements: ['survey' => '\d+'], methods: ['GET'])]
    public function index(Survey $survey): Response
    {
        return $this->render('share/index.html.twig', [
        	'title' => 'Share',
	        'survey' => $survey
        ]);
    }
}

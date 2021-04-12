<?php

namespace App\Controller;

use App\Entity\Person;
use App\Security\Authenticator;
use App\Services\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SignupController extends AbstractController
{
    #[Route('/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function index(Request $request, ValidationService $validationService, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guard, Authenticator $authenticator): Response
    {
	    $person = (new Person())
		    ->setEmail($request->get('email'))
		    ->setPassword($request->get('password'))
		    ->setPasswordConfirmation($request->get('password_confirmation'));
    	if ($request->isMethod('post')) {
		    $validationService->validate($person);
		    if (!$errors = $validationService->getErrors(Person::class)) {
			    $person->setRoles(['ROLE_USER'])
				    ->setPassword($passwordEncoder->encodePassword($person, $person->getPassword()));
			    $entityManager = $this->getDoctrine()->getManager();
			    $entityManager->persist($person);
			    $entityManager->flush();
			    $guard->authenticateUserAndHandleSuccess($person, $request, $authenticator, 'main');
			    return $this->redirectToRoute('home');
		    }
	    }
        return $this->render('signup/index.html.twig', [
            'title' => 'Sign Up',
	        'person' => $person,
	        'errors' => $errors ?? []
        ]);
    }
}

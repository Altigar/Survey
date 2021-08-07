<?php

namespace App\Controller;

use App\Entity\Person;
use App\Exception\ValidationException;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignupController extends AbstractController
{
	public function __construct(
		private ValidatorInterface $validator,
		private UserPasswordEncoderInterface $passwordEncoder,
		private GuardAuthenticatorHandler $guard,
		private Authenticator $authenticator,
		private EntityManagerInterface $entityManager,
	) {}

    #[Route('/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
	    $person = (new Person())
		    ->setEmail($request->get('email'))
		    ->setPassword($request->get('password'))
		    ->setPasswordConfirmation($request->get('password_confirmation'));
    	if ($request->isMethod('post')) {
		    try {
			    $violations = $this->validator->validate($person);
			    if ($violations->count()) {
				    throw new ValidationException($violations);
			    }
			    $person->setRoles(['ROLE_USER'])
				    ->setPassword($this->passwordEncoder->encodePassword($person, $person->getPassword()));
			    $this->entityManager->persist($person);
			    $this->entityManager->flush();
			    $this->guard->authenticateUserAndHandleSuccess($person, $request, $this->authenticator, 'main');
			    return $this->redirectToRoute('home');
		    } catch (ValidationException $exception) {
			    $errors = $exception->getErrors();
		    }
	    }
        return $this->render('signup/index.html.twig', [
            'title' => 'Sign Up',
	        'person' => $person,
	        'errors' => $errors ?? []
        ]);
    }
}

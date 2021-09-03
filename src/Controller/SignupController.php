<?php

namespace App\Controller;

use App\Entity\Person;
use App\Exception\ValidationException;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignupController extends AbstractController implements CsrfTokenControllerInterface
{
	public function __construct(
		private ValidatorInterface $validator,
		private UserPasswordHasherInterface $passwordEncoder,
		private UserAuthenticatorInterface  $userAuthenticator,
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
				    ->setPassword($this->passwordEncoder->hashPassword($person, $person->getPassword()));
			    $this->entityManager->persist($person);
			    $this->entityManager->flush();
			    return $this->userAuthenticator->authenticateUser($person, $this->authenticator, $request);
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

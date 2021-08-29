<?php

namespace App\Validator;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AmountValidator extends ConstraintValidator
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {}

	public function validate($value, Constraint $constraint)
	{
		$amount = $this->entityManager->getRepository(Question::class)->count(['survey' => $value->getSurvey()]);
		if ($amount > $constraint->value) {
			$this->context->buildViolation($constraint->message)
				->setParameter('{{ limit }}', $constraint->value)
				->atPath($constraint->path)
				->addViolation();
		}
	}
}

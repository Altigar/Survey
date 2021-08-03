<?php

namespace App\Validator\Pass;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RequiredValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if ($value->getIsRequired() && !$value->getAnswers()) {
			$this->context->buildViolation($constraint->message)
				->atPath('error')
				->addViolation();
		}
	}
}
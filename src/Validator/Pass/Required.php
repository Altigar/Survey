<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute] class Required extends Constraint
{
	public string $message = 'Field is required';

	public function getTargets(): array|string
	{
		return self::CLASS_CONSTRAINT;
	}

	public function validatedBy(): string
	{
		return static::class.'Validator';
	}
}
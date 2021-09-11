<?php

namespace App\Validator\Pass;

use Symfony\Component\Validator\Constraint;

#[\Attribute] class Required extends Constraint
{
	public string $message;

	public function __construct(string $message = 'Field is required', array $groups = null)
	{
		parent::__construct(groups: $groups);
		$this->message = $message;
	}

	public function getTargets(): array|string
	{
		return self::CLASS_CONSTRAINT;
	}

	public function validatedBy(): string
	{
		return static::class.'Validator';
	}
}
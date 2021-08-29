<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute] class Amount extends Constraint
{
	public int $value;
	public string $message;
	public string $path;

	public function __construct(
		int $value = 0,
		string $message = 'The number of entities cannot be more than {{ limit }}',
		string $path = 'error',
		array $groups = null
	) {
		parent::__construct(groups: $groups);
		$this->value = $value;
		$this->message = $message;
		$this->path = $path;
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
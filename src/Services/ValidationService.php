<?php

namespace App\Services;

use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
	public function __construct(
		protected ValidatorInterface $validator,
		protected array $errors = [],
	) {}

	public function validate(object $entity): void
	{
		$errors = $this->validator->validate($entity);
		$normalizedErrors = [];
		if ($errors->count()) {
			foreach ($errors as $error) {
				$path = new PropertyPath($error->getPropertyPath());
				$elements = $path->getElements();
				if ($path->getParent()) {
					$normalizedErrors[$elements[0]][$elements[1]] = [$elements[2] => $error->getMessage()];
				} else {
					$normalizedErrors[$elements[0]] = $error->getMessage();
				}
			}
			$this->errors[$entity::class] = $normalizedErrors;
		}
	}

	public function getErrors(string $entity): array
	{
		return $this->errors[$entity] ?? [];
	}
}
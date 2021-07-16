<?php

namespace App\Services;

use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
	const ERROR_FIRST = 0;

	public function __construct(
		protected ValidatorInterface $validator,
	) {}

	public function validatePass(array $data): array
	{
		$errors = [];
		foreach ($data as $object) {
			$violationList = $this->validator->validate($object, groups: ['required', $object->getType()]);
			if ($violationList->has(self::ERROR_FIRST)) {
				$errors[$object->getId()]['error'] = $violationList->get(self::ERROR_FIRST)->getMessage();
			}
		}
		return array_filter($errors);
	}

	public function validate(object $entity, ?array $groups = null): array
	{
		$errors = $this->validator->validate($entity, groups: $groups);
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
		}
		return $normalizedErrors;
	}

}
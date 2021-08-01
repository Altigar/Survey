<?php

namespace App\Services;

use App\Data\Content\Update\QuestionData;
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

	public function validate(QuestionData $questionData, ?array $groups = null): array
	{
		$errors = $this->validator->validate($questionData, groups: $groups);
		$normalizedErrors = [];
		if ($errors->count()) {
			foreach ($errors as $error) {
				$path = new PropertyPath($error->getPropertyPath());
				$elements = $path->getElements();
				if ($path->getParent()) {
					if ($questionData->getType() == 'scale') {
						$normalizedErrors[$elements[2]] = $error->getMessage();
					} elseif (in_array($questionData->getType(), ['radio', 'checkbox'])) {
						$normalizedErrors[$elements[0]][$elements[1]] = [$elements[2] => $error->getMessage()];
					} elseif ($questionData->getType() == 'text') {
						$normalizedErrors[$elements[2]] = $error->getMessage();
					}
				} else {
					$normalizedErrors[$elements[0]] = $error->getMessage();
				}
			}
		}
		return $normalizedErrors;
	}
}
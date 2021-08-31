<?php

namespace App\Exception\Content;

use App\Entity\Question;
use App\Exception\ValidationException;
use Symfony\Component\PropertyAccess\PropertyPath;

class UpdateValidationException extends ValidationException
{
	public function getErrors(): array
	{
		$errors = [];
		if ($this->errors->count()) {
			foreach ($this->errors as $error) {
				$path = new PropertyPath($error->getPropertyPath());
				$elements = $path->getElements();
				if ($path->getParent()) {
					match ($error->getRoot()->getType()) {
						Question::TYPE_RADIO, Question::TYPE_CHECKBOX => $errors[$elements[0]][$elements[1]] = [$elements[2] => $error->getMessage()],
						Question::TYPE_SCALE, Question::TYPE_TEXT  => $errors[$elements[2]] = $error->getMessage(),
					};
				} else {
					$errors[$error->getPropertyPath()] = $error->getMessage();
				}
			}
		}
		return $errors;
	}
}
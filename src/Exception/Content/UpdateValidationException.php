<?php

namespace App\Exception\Content;

use App\Entity\Question;
use App\Exception\ValidationException;
use App\Exception\ValidationExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UpdateValidationException extends ValidationException implements ValidationExceptionInterface
{
	public function __construct(ConstraintViolationListInterface $errors, string $message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($errors, $message, $code, $previous);
		$this->errors = $errors;
	}

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
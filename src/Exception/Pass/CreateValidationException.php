<?php

namespace App\Exception\Pass;

use App\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CreateValidationException extends ValidationException
{
	public function __construct(ConstraintViolationListInterface $errors, string $message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($errors, $message, $code, $previous);
		$this->errors = $errors;
	}

	public function getErrors(): array
	{
		$errors = [];
		foreach ($this->errors as $error) {
			$errors[$error->getRoot()->getId()]['error'] = $error->getMessage();
		}
		return $errors;
	}
}
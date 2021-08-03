<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class ValidationException extends \Exception implements ValidationExceptionInterface
{
	protected ConstraintViolationListInterface $errors;

	public function __construct(ConstraintViolationListInterface $errors, string $message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->errors = $errors;
	}

	public function getErrors(): array
	{
		$errors = [];
		if ($this->errors->count()) {
			foreach ($this->errors as $error) {
				$errors[$error->getPropertyPath()] = $error->getMessage();
			}
		}
		return $errors;
	}
}
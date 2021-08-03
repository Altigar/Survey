<?php

namespace App\Exception\Content;

use App\Exception\ValidationException;
use App\Exception\ValidationExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CreateValidationException extends ValidationException implements ValidationExceptionInterface
{
	public function __construct(ConstraintViolationListInterface $errors, string $message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($errors, $message, $code, $previous);
		$this->errors = $errors;
	}
}
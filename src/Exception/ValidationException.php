<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends HttpException implements ValidationExceptionInterface
{
	protected ConstraintViolationListInterface $errors;

	public function __construct(
		ConstraintViolationListInterface $errors,
		?string $message = '',
		int $statusCode = 422,
		array $headers = [],
		\Throwable $previous = null,
		int $code = 0
	) {
		parent::__construct($statusCode, $message, $previous, $headers, $code);
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
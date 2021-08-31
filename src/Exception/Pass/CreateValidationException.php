<?php

namespace App\Exception\Pass;

use App\Exception\ValidationException;

class CreateValidationException extends ValidationException
{
	public function getErrors(): array
	{
		$errors = [];
		foreach ($this->errors as $error) {
			$errors[$error->getRoot()->getId()]['error'] = $error->getMessage();
		}
		return $errors;
	}
}
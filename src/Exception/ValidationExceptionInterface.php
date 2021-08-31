<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

interface ValidationExceptionInterface extends HttpExceptionInterface
{
	public function getErrors(): array;
}
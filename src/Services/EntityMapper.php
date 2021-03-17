<?php

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityMapper
{
	private ValidatorInterface $validator;
	private SerializerInterface $serializer;
	private CsrfTokenManagerInterface $tokenManager;
	private array $rawData = [];
	private array $errors = [];
	private array $entities = [];

	public function __construct(
		ValidatorInterface $validator,
		SerializerInterface $serializer,
		CsrfTokenManagerInterface $tokenManager
	) {
		$this->validator = $validator;
		$this->serializer = $serializer;
		$this->tokenManager = $tokenManager;
	}

	public function validate(string $json, string $model): void
	{
		if (!$json) {
			throw new BadRequestHttpException('Empty body');
		}

		try {
			$this->entities[$model] = $this->serializer->deserialize($json, $model, 'json');
			$this->rawData[$model] = json_decode($json, true);
		} catch (\Exception $e) {
			throw new BadRequestHttpException('Invalid body');
		}

		$errors = $this->validator->validate($this->getEntity($model));
		if ($errors->count()) {
			foreach ($errors as $error) {
				$this->errors[$model][$error->getPropertyPath()] = $error->getMessage();
			}
		}
	}

	public function getRawData(string $model): ?array
	{
		return $model ? $this->rawData[$model] ?? null : $this->rawData;
	}

	public function getErrors(string $model): array
	{
		return $this->errors[$model] ?? [];
	}

	public function getEntity(string $name): object
	{
		return $this->entities[$name];
	}
}

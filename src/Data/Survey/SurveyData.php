<?php

namespace App\Data\Survey;

use Symfony\Component\Validator\Constraints as Assert;

final class SurveyData
{
	#[Assert\Length(min: 1, max: 255)]
	private string $name;

	#[Assert\Length(max: 400)]
	private ?string $description;
	private bool $repeatable;

	public function __construct(string $name, ?string $description = null, bool $repeatable = false)
	{
		$this->name = trim($name);
		$this->description = trim($description);
		$this->repeatable = $repeatable;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getRepeatable(): bool
	{
		return $this->repeatable;
	}
}

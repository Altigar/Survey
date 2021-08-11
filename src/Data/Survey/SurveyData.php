<?php

namespace App\Data\Survey;

use Symfony\Component\Validator\Constraints as Assert;

class SurveyData
{
	#[Assert\Length(min: 1, max: 255)]
	private string $name;

	#[Assert\Length(min: 1, max: 400)]
	private ?string $description;
	private bool $repeatable;

	public function __construct(string $name, string $description = null, bool $repeatable = false)
	{
		$this->name = $name;
		$this->description = $description;
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

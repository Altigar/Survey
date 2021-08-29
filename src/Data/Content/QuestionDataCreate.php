<?php

namespace App\Data\Content;

use App\Entity\Question;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;

#[CustomAssert\Amount(value: 25, message: 'The number of questions cannot be more than {{ limit }}', groups: ['default'])]
final class QuestionDataCreate
{
	#[Assert\Choice([
		Question::TYPE_RADIO,
		Question::TYPE_CHECKBOX,
		Question::TYPE_STRING,
		Question::TYPE_TEXT,
		Question::TYPE_SCALE
	], groups: ['default'])]
	private string $type;

	#[Assert\Positive(groups: ['default'])]
	private int $ordering;
	private int $survey;

	public function __construct(string $type, int $ordering, int $survey)
	{
		$this->type = $type;
		$this->ordering = $ordering;
		$this->survey = $survey;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getOrdering(): int
	{
		return $this->ordering;
	}

	public function getSurvey(): int
	{
		return $this->survey;
	}
}

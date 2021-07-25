<?php

namespace App\Data\Content\Create;

use App\Entity\Question;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionData
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

	public function __construct(string $type, int $ordering)
	{
		$this->type = $type;
		$this->ordering = $ordering;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getOrdering(): int
	{
		return $this->ordering;
	}
}

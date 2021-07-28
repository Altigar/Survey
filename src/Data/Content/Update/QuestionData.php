<?php

namespace App\Data\Content\Update;

use App\Entity\Question;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionData
{
	#[Assert\Positive(groups: ['default'])]
	private int $id;
	private bool $is_required;

	#[Assert\Choice([
		Question::TYPE_RADIO,
		Question::TYPE_CHECKBOX,
		Question::TYPE_STRING,
		Question::TYPE_TEXT,
		Question::TYPE_SCALE
	], groups: ['default'])]
	private string $type;
	private string $text;

	#[Assert\Positive(groups: ['default'])]
	private int $ordering;
	private ?array $options;

	public function __construct(
		int $id,
		bool $isRequired,
		string $text,
		string $type,
		int $ordering,
		?array $options = null
	) {
		$this->id = $id;
		$this->is_required = $isRequired;
		$this->type = $type;
		$this->text = $text;
		$this->ordering = $ordering;
		$this->options = $options;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getIsRequired(): bool
	{
		return $this->is_required;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function getOrdering(): int
	{
		return $this->ordering;
	}

	public function getOptions(): ?array
	{
		return $this->options;
	}

	public function addOption(OptionData $optionData): self
	{
		return $this;
	}
}

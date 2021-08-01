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
	], message: 'Type {{ value }} does not exist', groups: ['default'])]
	private string $type;

	#[Assert\Length(min: 1, max: 300, groups: ['default'])]
	private string $text;

	#[Assert\Positive(groups: ['default'])]
	private int $ordering;

	#[Assert\Valid(groups: [
		Question::TYPE_RADIO,
		Question::TYPE_CHECKBOX,
		Question::TYPE_TEXT,
		Question::TYPE_SCALE,
		'default',
	])]
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

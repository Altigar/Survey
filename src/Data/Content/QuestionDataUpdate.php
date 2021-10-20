<?php

namespace App\Data\Content;

use App\Entity\Question;
use Symfony\Component\Validator\Constraints as Assert;

final class QuestionDataUpdate
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

	#[Assert\Valid(groups: [Question::TYPE_RADIO, Question::TYPE_CHECKBOX, 'default'])]
	#[Assert\Count(
		min: 1,
		max: 25,
		minMessage: 'You must specify at least one option',
		maxMessage: 'You cannot specify more than {{ limit }} options',
		groups: ['default']
	)]
	private ?array $options;

	#[Assert\Range(min: 1, max: 10, groups: [Question::TYPE_TEXT])]
	private ?int $row;

	#[Assert\Positive(groups: [Question::TYPE_SCALE])]
	#[Assert\Range(min: 2, max: 10, groups: [Question::TYPE_SCALE])]
	private ?int $scale;

	#[Assert\Length(max: 40, groups: [Question::TYPE_SCALE])]
	private ?string $scale_from_text;

	#[Assert\Length(max: 40, groups: [Question::TYPE_SCALE])]
	private ?string $scale_to_text;

	public function __construct(
		int $id,
		bool $isRequired,
		string $text,
		string $type,
		int $ordering,
		?array $options = null,
		?int $row = null,
		?int $scale = null,
		?string $scale_from_text = null,
		?string $scale_to_text = null
	) {
		$this->id = $id;
		$this->is_required = $isRequired;
		$this->type = $type;
		$this->text = trim($text);
		$this->ordering = $ordering;
		$this->options = $options;
		$this->row = $row;
		$this->scale = $scale;
		$this->scale_from_text = trim($scale_from_text);
		$this->scale_to_text = trim($scale_to_text);
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

	public function getRow(): ?int
	{
		return $this->row;
	}

	public function getScale(): ?int
	{
		return $this->scale;
	}

	public function getScaleFromText(): ?string
	{
		return $this->scale_from_text;
	}

	public function getScaleToText(): ?string
	{
		return $this->scale_to_text;
	}

	public function addOption(OptionDataUpdate $optionData): self
	{
		return $this;
	}
}

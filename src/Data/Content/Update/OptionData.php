<?php

namespace App\Data\Content\Update;

use App\Entity\Question;
use Symfony\Component\Validator\Constraints as Assert;

class OptionData
{
	private ?int $id;

	#[Assert\Positive(groups: ['default'])]
	private int $ordering;

	#[Assert\Length(min: 1, max: 300, groups: [Question::TYPE_RADIO, Question::TYPE_CHECKBOX])]
	private ?string $text;

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
		?int $id,
		int $ordering,
		?string $text,
		?int $row,
		?int $scale,
		?string $scale_from_text,
		?string $scale_to_text
	) {
		$this->id = $id;
		$this->ordering = $ordering;
		$this->text = $text;
		$this->row = $row;
		$this->scale = $scale;
		$this->scale_from_text = $scale_from_text;
		$this->scale_to_text = $scale_to_text;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getOrdering(): int
	{
		return $this->ordering;
	}

	public function getText(): ?string
	{
		return $this->text;
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
}
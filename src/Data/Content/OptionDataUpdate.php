<?php

namespace App\Data\Content;

use App\Entity\Question;
use Symfony\Component\Validator\Constraints as Assert;

final class OptionDataUpdate
{
	private ?int $id;

	#[Assert\Positive(groups: ['default'])]
	private int $ordering;

	#[Assert\Length(min: 1, max: 50, groups: [Question::TYPE_RADIO, Question::TYPE_CHECKBOX])]
	private ?string $text;

	public function __construct(?int $id, int $ordering, ?string $text)
	{
		$this->id = $id;
		$this->ordering = $ordering;
		$this->text = trim($text);
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
}
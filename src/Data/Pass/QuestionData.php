<?php

namespace App\Data\Pass;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as PassAssert;

#[PassAssert\Required(groups: ['required'])]
final class QuestionData
{
	private int $id;
	private bool $is_required;
	private string $type;

	#[Assert\Valid]
	private ?array $answers;
	
	public function __construct(int $id, bool $is_required, string $type, ?array $answers)
	{
		$this->id = $id;
		$this->is_required = $is_required;
		$this->type = $type;
		$this->answers = $answers;
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

	public function getAnswers(): ?array
	{
		return $this->answers;
	}

	public function addAnswer(AnswerData $answer): self
	{
		return $this;
	}
}

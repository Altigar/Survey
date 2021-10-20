<?php

namespace App\Data\Pass;

use Symfony\Component\Validator\Constraints as Assert;

final class AnswerData
{
	private OptionData $option;

	#[Assert\Length(
		max: 350,
		maxMessage: 'This value is too long. It should have {{ limit }} character or less',
		groups: ['string', 'text'],
	)]
	private ?string $text;

	#[Assert\Positive(groups: ['scale'])]
	#[Assert\NotBlank(groups: ['scale'])]
	private ?int $scale_value;

	public function __construct(OptionData $option, ?string $text, ?int $scale_value)
	{
		$this->option = $option;
		$this->text = trim($text);
		$this->scale_value = $scale_value;
	}

	public function getOption(): OptionData
	{
		return $this->option;
	}

	public function getText(): ?string
	{
		return $this->text;
	}

	public function getScaleValue(): ?int
	{
		return $this->scale_value;
	}
}

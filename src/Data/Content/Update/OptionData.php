<?php

namespace App\Data\Content\Update;

class OptionData
{
	private ?int $id;
	private int $ordering;
	private ?string $text;
	private ?int $row;
	private ?int $scale;
	private ?string $scale_from_text;
	private ?string $scale_to_text;

	public function __construct(
		?int $id,
		int $ordering,
		?string $text,
		int|string|null $row,
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
<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OptionRepository::class)
 */
class Option
{
	public const DEFAULT_ROW = 1;
	public const DEFAULT_SCALE = 10;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	#[Assert\NotBlank(groups: ['choice'])]
	#[Assert\Length(max: 100, maxMessage: 'Your option cannot be longer than {{ limit }} characters')]
    private ?string $text = null;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="options", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Question $question = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $ordering = 1;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="option")
     */
    private ArrayCollection|PersistentCollection|null $answers = null;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $scale = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
	#[Assert\Length(max: 40, maxMessage: 'Text cannot be longer than {{ limit }} characters')]
    private ?string $scale_from_text = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
	#[Assert\Length(max: 40, maxMessage: 'Text cannot be longer than {{ limit }} characters')]
    private ?string $scale_to_text = null;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    #[Assert\NotBlank(groups: ['text'])]
    #[Assert\Positive(groups: ['text'])]
    private ?int $row = null;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

	public static function createContent(?string $text = null, ?int $row = null, ?int $scale = null, int $ordering = 1): self
	{
		$option = new self;
		$option->text = $text;
		$option->row = $row;
		$option->scale = $scale;
		$option->ordering = $ordering;

		return $option;
    }

	public function updateContent(?int $row = null, ?string $text = null): self
	{
		$this->row = $row;
		$this->text = $text;

		return $this;
    }

	public function updateContentScale(int $scale, string $scale_from_text, string $scale_to_text): self
	{
		$this->scale = $scale;
		$this->scale_from_text = $scale_from_text;
		$this->scale_to_text = $scale_to_text;

		return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getOrdering(): int
    {
        return $this->ordering;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setOption($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getOption() === $this) {
                $answer->setOption(null);
            }
        }

        return $this;
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

	public function getRow(): ?int
	{
		return $this->row;
	}
}

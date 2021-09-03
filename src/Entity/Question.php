<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
	const TYPE_RADIO = 'radio';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_STRING = 'string';
	const TYPE_TEXT = 'text';
	const TYPE_SCALE = 'scale';

	const DEFAULT_ROW = 1;
	const DEFAULT_SCALE = 10;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

	/**
	 * @ORM\Column(type="text")
	 */
    private string $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Survey::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Survey $survey;

    /**
     * @ORM\OneToMany(targetEntity=Option::class, mappedBy="question", orphanRemoval=true, cascade={"persist", "remove"})
     */
	private ?Collection $options;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $type;

    /**
     * @ORM\Column(type="integer")
     */
    private int $ordering;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="question", cascade={"persist", "remove"})
     */
    private ?Collection $answers;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $is_required;

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private ?int $row = null;

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private ?int $scale = null;

	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	private ?string $scale_from_text = null;

	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	private ?string $scale_to_text = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->answers = new ArrayCollection();
    }

	public static function create(Survey $survey, string $type, int $ordering): self
	{
		$question = new self();
		$question->survey = $survey;
		$question->type = $type;
		$question->text = 'Question text';
		$question->ordering = $ordering;
		$question->created_at = new \DateTime('now');
		$question->is_required = false;

		return $question;
	}

	public function textType(?int $row): self
	{
		$this->row = $row;

		return $this;
	}

	public function scaleType(?int $scale = null, ?string $scale_from_text = null, ?string $scale_to_text = null): self
	{
		$this->scale = $scale;
		$this->scale_from_text = $scale_from_text;
		$this->scale_to_text = $scale_to_text;

		return $this;
	}

	public function update(bool $is_required, string $text): self
	{
		$this->is_required = $is_required;
		$this->text = $text;

		return $this;
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    public function getOptions(): ArrayCollection|PersistentCollection|null
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setQuestion($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getQuestion() === $this) {
                $option->setQuestion(null);
            }
        }

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
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
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

	public function getIsRequired(): bool
	{
		return $this->is_required;
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

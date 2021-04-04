<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

	/**
	 * @Assert\NotBlank
	 * @Assert\Length(
	 *     max = 255,
	 *     maxMessage = "Your question cannot be longer than {{ limit }} characters"
	 * )
	 * @ORM\Column(type="text", nullable=true)
	 */
    private ?string $text = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $created_at = null;

    /**
     * @ORM\ManyToOne(targetEntity=Survey::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Survey $survey = null;

    /**
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity=Option::class, mappedBy="question", orphanRemoval=true, cascade={"persist", "remove"}, fetch="EAGER")
     */
	private ArrayCollection|PersistentCollection|null $options = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $ordering = null;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $row = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getSurvey(): ?Survey
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOrdering(): ?int
    {
        return $this->ordering;
    }

    public function setOrdering(?int $ordering): self
    {
        $this->ordering = $ordering;

        return $this;
    }

    public function getRow(): ?int
    {
        return $this->row;
    }

    public function setRow(?int $row): self
    {
        $this->row = $row;

        return $this;
    }
}

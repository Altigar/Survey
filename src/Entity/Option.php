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
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="options", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Question $question = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $ordering = 1;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="option")
     */
    private ?Collection $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

	public static function create(string $text = 'First option', int $ordering = 1): self
	{
		$option = new self;
		$option->text = $text;
		$option->ordering = $ordering;

		return $option;
    }

	public function updateContent(?int $row = null, ?string $text = null): self
	{
		$this->row = $row;
		$this->text = $text;

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
}

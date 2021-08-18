<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $person;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Question $question;

    /**
     * @ORM\ManyToOne(targetEntity=Option::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Option $option;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $text = null;

    /**
     * @ORM\ManyToOne(targetEntity=Pass::class, inversedBy="answers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Pass $pass;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $scale_value = null;

	public static function create(Person $person, Question $question, Pass $pass): self
	{
		$answer = new self();
		$answer->person = $person;
		$answer->question = $question;
		$answer->pass = $pass;

		return $answer;
    }

	public function choiceType(Option $option): self
	{
		$this->option = $option;

		return $this;
    }

	public function textType(string $text): self
	{
		$this->text = $text;

		return $this;
    }

	public function scaleType(int $scale_value): self
	{
		$this->scale_value = $scale_value;

		return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
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

    public function getOption(): ?Option
    {
        return $this->option;
    }

	public function setOption(?Option $option): self
	{
		$this->option = $option;

		return $this;
	}

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getPass(): ?Pass
    {
        return $this->pass;
    }

	public function getScaleValue(): ?int
	{
		return $this->scale_value;
	}
}

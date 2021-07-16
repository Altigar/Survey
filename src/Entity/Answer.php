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
    private Question $question;

    /**
     * @ORM\ManyToOne(targetEntity=Option::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private Option $option;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $text = null;

    /**
     * @ORM\ManyToOne(targetEntity=Pass::class, inversedBy="answers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $pass;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $scale_value = null;

	public static function create(array $data): self
	{
		$self = new self();
		$self->person = $data['person'];
		$self->option = $data['option'];
		$self->question = $data['question'];
		$self->pass = $data['pass'];

		$self->text = $data['text'] ?? null;
		$self->scale_value = $data['scale_value'] ?? null;

		return $self;
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

    public function getOption(): ?Option
    {
        return $this->option;
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

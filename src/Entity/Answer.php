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
    private ?Person $person = null;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Question $question = null;

    /**
     * @ORM\ManyToOne(targetEntity=Option::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Option $option;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $text = null;

    /**
     * @ORM\ManyToOne(targetEntity=Pass::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pass;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
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

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getPass(): ?Pass
    {
        return $this->pass;
    }

    public function setPass(?Pass $pass): self
    {
        $this->pass = $pass;

        return $this;
    }
}

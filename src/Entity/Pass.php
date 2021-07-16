<?php

namespace App\Entity;

use App\Repository\PassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PassRepository::class)
 */
class Pass
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Survey::class, inversedBy="passes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $survey;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="passes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="pass", orphanRemoval=true)
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

	public static function create(Survey $survey, Person $person): self
	{
		$self = new self();
		$self->survey = $survey;
		$self->person = $person;
		$self->created_at = new \DateTime('now');

		return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setPass($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getPass() === $this) {
                $answer->setPass(null);
            }
        }

        return $this;
    }
}

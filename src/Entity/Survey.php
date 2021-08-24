<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=SurveyRepository::class)
 */
class Survey
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="survey", cascade={"persist", "remove"})
     */
	private ?Collection $questions;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="surveys")
     * @ORM\JoinColumn(nullable=false)
     */
    private UserInterface $person;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\OneToMany(targetEntity=Pass::class, mappedBy="survey", orphanRemoval=true)
     */
    private ?Collection $passes;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $repeatable;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private string $hash;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->passes = new ArrayCollection();
    }

	public static function create(UserInterface $person, string $name, ?string $description = null, bool $repeatable = false): self
	{
		$survey = new self();
		$survey->created_at = new \DateTime('now');
		$survey->person = $person;
		$survey->name = $name;
		$survey->description = $description;
		$survey->repeatable = $repeatable;
		$survey->hash = bin2hex(random_bytes(8));

		return $survey;
    }

	public function update(string $name, ?string $description, bool $repeatable = false): void
	{
		$this->name = $name;
		$this->description = $description;
		$this->repeatable = $repeatable;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function getQuestions(): ?Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setSurvey($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getSurvey() === $this) {
                $question->setSurvey(null);
            }
        }

        return $this;
    }

    public function getPerson(): UserInterface
    {
        return $this->person;
    }

    public function setPerson(UserInterface $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPasses(): ?Collection
    {
        return $this->passes;
    }

    public function addPass(Pass $pass): self
    {
        if (!$this->passes->contains($pass)) {
            $this->passes[] = $pass;
            $pass->setSurvey($this);
        }

        return $this;
    }

    public function removePass(Pass $pass): self
    {
        if ($this->passes->removeElement($pass)) {
            // set the owning side to null (unless already changed)
            if ($pass->getSurvey() === $this) {
                $pass->setSurvey(null);
            }
        }

        return $this;
    }

    public function getRepeatable(): bool
    {
        return $this->repeatable;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}

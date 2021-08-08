<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
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
    private ?DateTimeInterface $created_at = null;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="survey", cascade={"persist", "remove"})
     */
	private ArrayCollection|PersistentCollection|null $questions = null;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="surveys", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?UserInterface $person = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\OneToMany(targetEntity=Pass::class, mappedBy="survey", orphanRemoval=true)
     */
    private $passes;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->passes = new ArrayCollection();
    }

	public static function create(UserInterface $person, string $name, string $description): self
	{
		$survey = new self();
		$survey->created_at = new \DateTime('now');
		$survey->person = $person;
		$survey->name = $name;
		$survey->description = $description;

		return $survey;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function getQuestions(): Collection|PersistentCollection|null
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

    public function getPerson(): ?UserInterface
    {
        return $this->person;
    }

    public function setPerson(?UserInterface $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return Collection|Pass[]
     */
    public function getPasses(): Collection
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
}

<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=PersonRepository::class)
 */
#[UniqueEntity('email', message: 'This email is already used')]
class Person implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[Assert\NotBlank]
	#[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Assert\Length(max: 254, maxMessage: 'Email cannot be longer than {{ limit }} characters')]
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
	#[Assert\Length(
		min: 8,
		max: 126,
		minMessage: 'Your Password must be at least {{ limit }} characters long',
		maxMessage: 'Your Password cannot be longer than {{ limit }} characters',
	)]
    private ?string $password;

	#[Assert\NotBlank]
	#[Assert\Length(
		min: 8,
		max: 126,
		minMessage: 'Your Password must be at least {{ limit }} characters long',
		maxMessage: 'Your Password cannot be longer than {{ limit }} characters',
	)]
	private ?string $password_confirmation = null;

    /**
     * @ORM\OneToMany(targetEntity=Survey::class, mappedBy="person", orphanRemoval=true)
     */
    private ?Collection $surveys;

    /**
     * @ORM\OneToMany(targetEntity=Pass::class, mappedBy="person", orphanRemoval=true)
     */
    private ?Collection $passes;

    public function __construct()
    {
        $this->surveys = new ArrayCollection();
        $this->passes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = trim($email);

        return $this;
    }

	public function getUserIdentifier(): string
	{
		return (string) $this->email;
	}

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = trim($password);

        return $this;
    }

	public function getPasswordConfirmation(): ?string
	{
		return (string) $this->password_confirmation;
	}

	public function setPasswordConfirmation(?string $password_confirmation): self
	{
		$this->password_confirmation = trim($password_confirmation);

		return $this;
	}

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

	/**
	 * @param ExecutionContextInterface $context
	 */
	#[Assert\Callback]
	public function validate(ExecutionContextInterface $context): void
	{
		if ($this->password != $this->password_confirmation) {
			$context->buildViolation('Passwords must match')
				->atPath('password')
				->addViolation();
		}
	}

    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    public function addSurvey(Survey $survey): self
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys[] = $survey;
            $survey->setPerson($this);
        }

        return $this;
    }

    public function removeSurvey(Survey $survey): self
    {
        if ($this->surveys->removeElement($survey)) {
            // set the owning side to null (unless already changed)
            if ($survey->getPerson() === $this) {
                $survey->setPerson(null);
            }
        }

        return $this;
    }

    public function getPasses(): ?Collection
    {
        return $this->passes;
    }

    public function addPass(Pass $pass): self
    {
        if (!$this->passes->contains($pass)) {
            $this->passes[] = $pass;
            $pass->setPerson($this);
        }

        return $this;
    }

    public function removePass(Pass $pass): self
    {
        if ($this->passes->removeElement($pass)) {
            // set the owning side to null (unless already changed)
            if ($pass->getPerson() === $this) {
                $pass->setPerson(null);
            }
        }

        return $this;
    }
}

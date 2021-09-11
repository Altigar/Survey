<?php

namespace App\Entity;

use App\Repository\ExternalPersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExternalPersonRepository::class)
 */
class ExternalPerson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=40, unique="true")
     */
    private string $ip;

    /**
     * @ORM\OneToMany(targetEntity=Pass::class, mappedBy="externalPerson")
     */
    private ?Collection $passes;

    public function __construct()
    {
        $this->passes = new ArrayCollection();
    }

	public static function create(string $ip): self
	{
		$externalPerson = new self();
		$externalPerson->ip = $ip;

		return $externalPerson;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPasses(): Collection
    {
        return $this->passes;
    }

    public function addPass(Pass $pass): self
    {
        if (!$this->passes->contains($pass)) {
            $this->passes[] = $pass;
            $pass->setExternalPerson($this);
        }

        return $this;
    }

    public function removePass(Pass $pass): self
    {
        if ($this->passes->removeElement($pass)) {
            // set the owning side to null (unless already changed)
            if ($pass->getExternalPerson() === $this) {
                $pass->setExternalPerson(null);
            }
        }

        return $this;
    }
}

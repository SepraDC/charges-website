<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BankRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: ['get' => ['normalization_context' => ['groups' => 'bank:list']]],
    itemOperations: ['get' => ['normalization_context' => ['groups' => 'bank:item']]],
    order: ['name' => 'ASC'],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: BankRepository::class)]
class Bank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['bank:list', 'bank:item'])]
    private ?string $name;

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: Charge::class, orphanRemoval: true)]
    #[Groups(['bank:item'])]
    private Collection $charges;

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups(['bank:list', 'bank:item'])]
    private ?string $abbreviation;

    public function __construct()
    {
        $this->charges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCharges(): Collection
    {
        return $this->charges;
    }

    public function addCharge(Charge $charge): self
    {
        if (!$this->charges->contains($charge)) {
            $this->charges[] = $charge;
            $charge->setBank($this);
        }

        return $this;
    }

    public function removeCharge(Charge $charge): self
    {
        // set the owning side to null (unless already changed)
        if ($this->charges->removeElement($charge) && $charge->getBank() === $this) {
            $charge->setBank(null);
        }

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }
}

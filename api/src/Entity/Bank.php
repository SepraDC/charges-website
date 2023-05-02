<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\BankRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new Get(normalizationContext: ['groups' => 'user:readBankItem']),
        new GetCollection(normalizationContext: ['groups' => 'user:readBankList'])
    ],
    order: ['name' => 'ASC'],
    paginationEnabled: false,
)]
#[ApiResource(
    uriTemplate: '/bank/{bankId}/charges',
    operations: [new GetCollection()],
    uriVariables: [
        'bankId' => new Link(toProperty: 'charges', fromClass: Charge::class)
    ]
)]
#[ORM\Entity(repositoryClass: BankRepository::class)]
class Bank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:readBankItem', 'user:readBankList'])]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:readBankList', 'user:readBankItem'])]
    private ?string $name;

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: Charge::class, orphanRemoval: true)]
    #[Groups(['bank:item'])]
    private Collection $charges;

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups(['user:readBankList', 'user:readBankItem'])]
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

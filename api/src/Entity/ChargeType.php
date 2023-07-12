<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ChargeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Get(normalizationContext: ['groups' => 'user:readChargeTypeItem']),
        new GetCollection(normalizationContext: ['groups' => 'user:readChargeTypeList']),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    order: ['name' => 'ASC'],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: ChargeTypeRepository::class)]
class ChargeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["user:readChargeTypeList", "user:readChargeTypeItem"])]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["user:readChargeTypeList", "user:readChargeTypeItem", "user:chargeList"])]
    private ?string $name;

    #[ORM\OneToMany(mappedBy: 'chargeType', targetEntity: Charge::class, orphanRemoval: true)]
    private Collection $charges;

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
            $charge->setChargeType($this);
        }

        return $this;
    }

    public function removeCharge(Charge $charge): self
    {
        // set the owning side to null (unless already changed)
        if ($this->charges->removeElement($charge) && $charge->getChargeType() === $this) {
            $charge->setChargeType(null);
        }

        return $this;
    }
}

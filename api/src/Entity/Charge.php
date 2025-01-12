<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\ResetChargesController;
use App\Repository\ChargeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' =>"user:chargeList"], security: "is_granted('ROLE_USER')"),
        new Get(normalizationContext: ['groups' =>"user:chargeItem"], security: "is_granted('ROLE_USER') and object.user == user"),
        new Post(security: "is_granted('ROLE_USER')"),
        new Put(security: "is_granted('ROLE_USER') and object.user == user"),
        new Patch(uriTemplate: '/charges/reset', controller: ResetChargesController::class, security: "is_granted('ROLE_USER')", read: false),
        new Patch(security: "is_granted('ROLE_USER') and object.user == user" ),
        new Delete(security: "is_granted('ROLE_USER') and object.user == user"),
    ],
    order: ['name' => 'ASC'],
    paginationEnabled: false,
)]
#[ApiFilter(SearchFilter::class, properties: ["bank.id" => "exact"])]
#[ORM\Entity(repositoryClass: ChargeRepository::class)]
class Charge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:chargeList', 'user:chargeItem'])]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:chargeList', 'user:chargeItem'])]
    private ?string $name;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Groups(['user:chargeList', 'user:chargeItem'])]
    private ?float $amount;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['user:chargeList', 'user:chargeItem'])]
    private bool $state;

    #[ORM\ManyToOne(targetEntity: Bank::class, inversedBy: 'charges')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:chargeList', 'user:chargeItem'])]
    private ?Bank $bank;

    #[ORM\ManyToOne(targetEntity: ChargeType::class, inversedBy: 'charges')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['user:chargeList', 'user:chargeItem'])]
    private ?ChargeType $chargeType;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['user:chargeList', 'user:chargeItem'])]
    private ?\DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'charges')]
    public ?User $user;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getBank(): ?Bank
    {
        return $this->bank;
    }

    public function setBank(?Bank $bank): self
    {
        $this->bank = $bank;

        return $this;
    }

    public function getChargeType(): ?ChargeType
    {
        return $this->chargeType;
    }

    public function setChargeType(?ChargeType $chargeType): self
    {
        $this->chargeType = $chargeType;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

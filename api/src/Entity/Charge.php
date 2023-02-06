<?php

namespace App\Entity;

use App\Repository\ChargeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChargeRepository::class)]
class Charge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?float $amount;

    #[ORM\Column(type: 'boolean')]
    private bool $state;

    #[ORM\ManyToOne(targetEntity: Bank::class, inversedBy: 'charges')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bank $bank;

    #[ORM\ManyToOne(targetEntity: ChargeType::class, inversedBy: 'charges')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ChargeType $chargeType;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'charges')]
    private ?User $user;


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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
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

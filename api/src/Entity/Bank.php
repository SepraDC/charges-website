<?php

namespace App\Entity;

use ApiPlatform\Metadata\GetCollection;
use App\Controller\BanksByUserController;
use App\Repository\BankRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/banks/user', controller: BanksByUserController::class, normalizationContext: ['groups' => 'user:readBankList']),
        new GetCollection(normalizationContext: ['groups' => 'user:readBankList'])
    ]
)]
#[ORM\Entity(repositoryClass: BankRepository::class)]
#[Vich\Uploadable]
class Bank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:readBankItem', 'user:readBankList', 'user:chargeList'])]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:readBankList', 'user:readBankItem', 'user:chargeList'])]
    private ?string $name;

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: Charge::class, orphanRemoval: true)]
    #[Groups(['bank:item'])]
    private Collection $charges;

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups(['user:readBankList', 'user:readBankItem'])]
    private ?string $abbreviation;

    #[Vich\UploadableField(mapping: 'bank_images', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageSize = null;

    /**
     * Virtual property for image URL
     */
    #[Groups(['user:readBankList', 'user:readBankItem'])]
    #[SerializedName('image')]
    private ?string $imageUrl = null;

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

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            $this->setImageSize($imageFile->getSize());
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }
}

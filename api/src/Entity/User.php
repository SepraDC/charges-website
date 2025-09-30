<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\UserRepository;
use App\State\UserVerifyProvider;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        operations: [
            new Get(),
            new Get(
                uriTemplate: "/verify",
                normalizationContext: ["groups" => "user:verify"],
                provider: UserVerifyProvider::class,
            ),
        ],
    ),
]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user:readUserItem", "user:readUserList", "user:verify"])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["user:readUserItem", "user:readUserList"])]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(["user:readUserItem", "user:readUserList", "user:verify"])]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Charge::class)]
    private Collection $charges;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    private ?string $plainPassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[Groups(["user:verify"])]
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Charge[]
     */
    public function getCharges(): Collection
    {
        return $this->charges;
    }

    public function addCharge(Charge $charge): self
    {
        if (!$this->charges->contains($charge)) {
            $this->charges[] = $charge;
            $charge->setUser($this);
        }

        return $this;
    }

    public function removeCharge(Charge $charge): self
    {
        // set the owning side to null (unless already changed)
        if (
            $this->charges->removeElement($charge) &&
            $charge->getUser() === $this
        ) {
            $charge->setUser(null);
        }

        return $this;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }


    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}

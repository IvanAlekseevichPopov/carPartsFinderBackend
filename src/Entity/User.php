<?php

declare(strict_types=1);

namespace App\Entity;

use App\DBAL\Types\Enum\UserRoleTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(
 *     name="`user`",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="email", columns={"email"}),
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected string $id;

    /**
     * @ORM\Column
     */
    private string $email;

    /**
     * @ORM\Column
     */
    private string $password;

    /**
     * @ORM\Column(type="json", options={"jsonb": true})
     */
    private array $roles = [UserRoleTypeEnum::ROLE_USER];

    public function __construct()
    {
        $this->id = Uuid::uuid7()->toString();
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return (string) $this->getEmail();
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function eraseCredentials()
    {
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(UserRoleTypeEnum $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function makeAdmin(): void
    {
        $this->addRole(UserRoleTypeEnum::ROLE_ADMIN);
    }

    public function removeRole(string $role): void
    {
        $this->roles = array_values(array_diff($this->roles, [$role]));
    }

    public function getId(): string
    {
        return $this->id;
    }
}

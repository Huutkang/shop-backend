<?php

namespace App\Entity;

use App\Repository\UserGroupRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
#[ORM\Table(name: 'user_groups')]
class UserGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Add getters and setters here

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}

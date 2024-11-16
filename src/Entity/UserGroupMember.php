<?php

namespace App\Entity;

use App\Repository\UserGroupMemberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupMemberRepository::class)]
#[ORM\Table(name: 'user_group_members')]
class UserGroupMember
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: UserGroup::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?UserGroup $group = null;

    // Add getters and setters here

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getGroup(): ?UserGroup
    {
        return $this->group;
    }

    public function setGroup(?UserGroup $group): static
    {
        $this->group = $group;

        return $this;
    }
}

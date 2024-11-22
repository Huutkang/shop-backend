<?php

namespace App\Entity;

use App\Repository\GroupPermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupPermissionRepository::class)]
#[ORM\Table(name: 'group_permissions')]
class GroupPermission
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Group $group = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Permission::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Permission $permission = null;

    // Add getters and setters here

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    public function setPermission(?Permission $permission): static
    {
        $this->permission = $permission;

        return $this;
    }
}

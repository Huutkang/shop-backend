<?php

namespace App\Entity;

use App\Repository\GroupPermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupPermissionRepository::class)]
#[ORM\Table(name: 'group_permissions')]
class GroupPermission
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: UserGroup::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?UserGroup $group = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Permission::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Permission $permission = null;

    // Add getters and setters here
}

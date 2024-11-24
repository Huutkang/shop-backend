<?php

namespace App\Dto;

use App\Entity\User;

class UserDto
{
    public int $id;
    public string $username;
    public string $email;
    public ?string $phone;
    public ?string $address;
    public bool $isActive;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->username = $user->getUsername();
        $this->email = $user->getEmail();
        $this->phone = $user->getPhone();
        $this->address = $user->getAddress();
        $this->isActive = $user->isActive();
        $this->createdAt = $user->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updatedAt = $user->getUpdatedAt()->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $user->setUsername($data['username'] ?? throw new \Exception('Username is required'))
             ->setEmail($data['email'] ?? throw new \Exception('Email is required'))
             ->setPassword($data['password'] ?? throw new \Exception('Password is required'))
             ->setPhone($data['phone'] ?? null)
             ->setAddress($data['address'] ?? null)
             ->setRole($data['role'] ?? 'customer')
             ->setActive($data['isActive'] ?? true)
             ->setCreatedAt(new \DateTime())
             ->setUpdatedAt(new \DateTime());

        return $user;
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->setUsername($data['username'] ?? $user->getUsername())
             ->setEmail($data['email'] ?? $user->getEmail())
             ->setPassword($data['password'] ?? $user->getPassword())
             ->setPhone($data['phone'] ?? $user->getPhone())
             ->setAddress($data['address'] ?? $user->getAddress())
             ->setRole($data['role'] ?? $user->getRole())
             ->setActive($data['isActive'] ?? $user->isActive())
             ->setUpdatedAt(new \DateTime());

        return $user;
    }

    public function deleteUser(int $id): void
    {
        $user = $this->getUserById($id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $this->entityManager->remove($user);
    }
}

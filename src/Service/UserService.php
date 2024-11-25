<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AppException;



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
        return $this->entityManager->getRepository(User::class)->findBy(
            ['isActive' => true], // Chỉ lấy tài khoản đang hoạt động
            ['id' => 'ASC']       // Sắp xếp theo ID tăng dần
        );
    }
    
    public function getUserById(int $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(
            ['id' => $id, 'isActive' => true] // Chỉ tìm nếu tài khoản đang hoạt động
        );
    }
    
    public function getUserByUsername(string $username): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(
            ['username' => $username, 'isActive' => true] // Chỉ tìm nếu tài khoản đang hoạt động
        );
    }
    
    public function getUserByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => $email, 'isActive' => true] // Chỉ tìm nếu tài khoản đang hoạt động
        );
    }
    
    public function createUser(array $data): User
    {
        $user = new User();
        $user->setUsername($data['username'] ?? throw new AppException('E1010'))
             ->setEmail($data['email'] ?? throw new AppException('E1011'))
             ->setPassword(password_hash($data['password'] ?? throw new AppException('E1014'), PASSWORD_BCRYPT))
             ->setPhone($data['phone'] ?? null)
             ->setAddress($data['address'] ?? null)
             ->setActive($data['isActive'] ?? true)
             ->setCreatedAt(new \DateTime())
             ->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['phone'])) {
            $user->setPhone($data['phone']);
        }
        if (isset($data['address'])) {
            $user->setAddress($data['address']);
        }
        if (isset($data['isActive'])) {
            $user->setActive($data['isActive']);
        }
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(int $id): void
    {
        $user = $this->getUserById($id);

        if (!$user) {
            throw new AppException('E1004');
        }

        if ($user->getUsername() === 'superadmin') {
            throw new AppException('E10101');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function verifyUserPassword(string $username, string $password): User
    {
        $user = $this->getUserByUsername($username);
        if (!$user) {
            throw new AppException('E1013'); // User not found
        }
        $isValid = password_verify($password, $user->getPassword());
        if ($isValid) {
            return $user;
        } else {
                throw new AppException('E1005');
        }
    }

    public function checkPassword(User $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }

}

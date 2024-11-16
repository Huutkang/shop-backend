<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private ?float $totalAmount = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $status = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['default' => 0.00])]
    private float $shippingFee = 0.00;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['default' => 0.00])]
    private float $discount = 0.00;

    #[ORM\ManyToOne(targetEntity: Coupon::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Coupon $coupon = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $updatedAt;

    // Add getters and setters here
}

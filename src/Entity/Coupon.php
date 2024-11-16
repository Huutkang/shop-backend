<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ORM\Table(name: 'coupons')]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private ?float $discount = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $startDate;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $endDate;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    // Add getters and setters here
}

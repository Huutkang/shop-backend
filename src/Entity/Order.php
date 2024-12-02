<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private ?string $paymentMethod = null; // Hình thức thanh toán

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private ?string $shippingStatus = 'pending'; // Trạng thái vận chuyển

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private ?string $paymentStatus = 'pending'; // Trạng thái thanh toán

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getShippingStatus(): ?string
    {
        return $this->shippingStatus;
    }

    public function setShippingStatus(string $shippingStatus): static
    {
        $this->shippingStatus = $shippingStatus;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getShippingFee(): ?float
    {
        return $this->shippingFee;
    }

    public function setShippingFee(float $shippingFee): static
    {
        $this->shippingFee = $shippingFee;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(?Coupon $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $reference;

    #[ORM\Column(type: 'string', length: 255)]
    private $customerEmail;

    #[ORM\Column(type: 'float')]
    private $total;

    #[ORM\Column(type: 'string', length: 50)]
    private $status = 'pending'; // pending, paid, cancelled, refunded

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'orderRef', targetEntity: OrderItem::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private $items;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $paypalPaymentId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $paypalPayerId;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    private $user;

    #[ORM\Column(type: 'string', length: 100)]
    private $deliveryFirstname;

    #[ORM\Column(type: 'string', length: 100)]
    private $deliveryLastname;

    #[ORM\Column(type: 'string', length: 255)]
    private $deliveryAddress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $deliveryAddress2;

    #[ORM\Column(type: 'string', length: 10)]
    private $deliveryPostalCode;

    #[ORM\Column(type: 'string', length: 100)]
    private $deliveryCity;

    #[ORM\Column(type: 'string', length: 100)]
    private $deliveryCountry;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrderRef($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrderRef() === $this) {
                $item->setOrderRef(null);
            }
        }

        return $this;
    }

    public function getPaypalPaymentId(): ?string
    {
        return $this->paypalPaymentId;
    }

    public function setPaypalPaymentId(?string $paypalPaymentId): self
    {
        $this->paypalPaymentId = $paypalPaymentId;

        return $this;
    }

    public function getPaypalPayerId(): ?string
    {
        return $this->paypalPayerId;
    }

    public function setPaypalPayerId(?string $paypalPayerId): self
    {
        $this->paypalPayerId = $paypalPayerId;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDeliveryFirstname(): ?string
    {
        return $this->deliveryFirstname;
    }

    public function setDeliveryFirstname(string $deliveryFirstname): self
    {
        $this->deliveryFirstname = $deliveryFirstname;
        return $this;
    }

    public function getDeliveryLastname(): ?string
    {
        return $this->deliveryLastname;
    }

    public function setDeliveryLastname(string $deliveryLastname): self
    {
        $this->deliveryLastname = $deliveryLastname;
        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    public function getDeliveryAddress2(): ?string
    {
        return $this->deliveryAddress2;
    }

    public function setDeliveryAddress2(?string $deliveryAddress2): self
    {
        $this->deliveryAddress2 = $deliveryAddress2;
        return $this;
    }

    public function getDeliveryPostalCode(): ?string
    {
        return $this->deliveryPostalCode;
    }

    public function setDeliveryPostalCode(string $deliveryPostalCode): self
    {
        $this->deliveryPostalCode = $deliveryPostalCode;
        return $this;
    }

    public function getDeliveryCity(): ?string
    {
        return $this->deliveryCity;
    }

    public function setDeliveryCity(string $deliveryCity): self
    {
        $this->deliveryCity = $deliveryCity;
        return $this;
    }

    public function getDeliveryCountry(): ?string
    {
        return $this->deliveryCountry;
    }

    public function setDeliveryCountry(string $deliveryCountry): self
    {
        $this->deliveryCountry = $deliveryCountry;
        return $this;
    }
}

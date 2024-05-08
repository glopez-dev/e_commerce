<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $_user = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $_product = null;


    public function toArray(): array
    {
        return $this->getProduct()->toArray();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->_user;
    }

    public function setUser(?User $_user): static
    {
        $this->_user = $_user;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->_product;
    }

    public function setProduct(?Product $_product): static
    {
        $this->_product = $_product;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $creationDate = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'order')]
    private Collection $products;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @param User $user
     * @param array<int, Product> $products
     */
    public function __construct(User $user, array $products)
    {
        $this->products = new ArrayCollection();
        $this->setUser($user);

        foreach ($products as $product) {
            $this->addProduct($product);
        }

        $this->setTotalPrice($this->computeTotalPrice());
        $this->setCreationDate(new \DateTimeImmutable());
    }

    public function toArray(): array
    {
        $products = array_map(
            fn (Product $product) => $product->toArray(),
            $this->getProducts()->toArray()
        );

        return [
            'id' => $this->getId(),
            'totalPrice' => $this->getTotalPrice(),
            'creationDate' => $this->getCreationDate(),
            'products' => $products,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function computeTotalPrice(): ?float
    {
        $totalPrice = 0;

        foreach ($this->getProducts() as $product) {
            $totalPrice += $product->getPrice();
        }

        return $totalPrice;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeImmutable $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setOrder($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getOrder() === $this) {
                $product->setOrder(null);
            }
        }

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
}

<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\ManyToOne]
    private ?City $city = null;

    #[ORM\Column]
    private bool $payOnDelivery = false;

    #[ORM\OneToMany(targetEntity: CommanderProduits::class, mappedBy: 'commande', cascade: ['persist'], orphanRemoval: true)]
    private Collection $commanderProduits;

    #[ORM\Column]
    private float $total = 0;

    #[ORM\Column]
    private bool $isCompleted = false;

    #[ORM\Column]
    private bool $isPaymentCompleted = false;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    public function __construct()
    {
        $this->commanderProduits = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\Column(length: 255)]
    private string $status = "Pending"; 

    // ================= GETTERS / SETTERS =================

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $name): static
    {
        $this->firstName=$name;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastname): static
    {
        $this->lastName=$lastname;
        return $this;
    }

        public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone=$phone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse=$adresse;
        return $this;
    }

    public function getId(): ?int { return $this->id; }

    public function getClient(): ?Client { return $this->client; }

    public function setClient(?User $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getCity(): ?City { return $this->city; }

    public function setCity(?City $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt= $createdAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $prixTotal): static
    {
        $this->total = $prixTotal;
        return $this;
    }

    public function isPayOnDelivery(): bool
    {
        return $this->payOnDelivery;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function isPaymentCompleted(): bool
    {
        return $this->isPaymentCompleted;
    }

    /**
     * @return Collection<int, CommanderProduits>
     */
    public function getCommanderProduits(): Collection
    {
        return $this->commanderProduits;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): ?static
    {
        $this->email=$email;
        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;

        return $this;
    }


    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}

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
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?City $city = null;

    #[ORM\Column]
    private ?bool $payOnDelivery = null;

    /**
     * @var Collection<int, CommanderProduits>
     */
    #[ORM\OneToMany(targetEntity: CommanderProduits::class, mappedBy: 'commande')]
    private Collection $commanderProduits;

    #[ORM\Column]
    private ?float $prixTotal = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isCompleted = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $isPaymentCompleted = null;

    //getter and setter

    public function __construct()
    {
        $this->commanderProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }


    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function isPayOnDelivery(): ?bool
    {
        return $this->payOnDelivery;
    }

    public function setPayOnDelivery(bool $payOnDelivery): static
    {
        $this->payOnDelivery = $payOnDelivery;

        return $this;
    }

    /**
     * @return Collection<int, CommanderProduits>
     */
    public function getCommanderProduits(): Collection
    {
        return $this->commanderProduits;
    }

    public function addCommanderProduit(CommanderProduits $commanderProduit): static
    {
        if (!$this->commanderProduits->contains($commanderProduit)) {
            $this->commanderProduits->add($commanderProduit);
            $commanderProduit->setCommande($this);
        }

        return $this;
    }

    public function removeCommanderProduit(CommanderProduits $commanderProduit): static
    {
        if ($this->commanderProduits->removeElement($commanderProduit)) {
            // set the owning side to null (unless already changed)
            if ($commanderProduit->getCommande() === $this) {
                $commanderProduit->setCommande(null);
            }
        }

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(?bool $isCompleted): static
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isPaymentCompleted(): ?bool
    {
        return $this->isPaymentCompleted;
    }

    public function setIsPaymentCompleted(bool $isPaymentCompleted): static
    {
        $this->isPaymentCompleted = $isPaymentCompleted;

        return $this;
    }
}

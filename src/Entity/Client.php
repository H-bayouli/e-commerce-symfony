<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Avis::class)]
    private Collection $avis;

    public function __construct()
    {
        parent::__construct();
        $this->commandes = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setClient($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getClient() === $this) {
                $commande->setClient(null);
            }
        }
        return $this;
    }

    public function getNombreCommandes(): int
    {
        return $this->commandes->count();
    }

    public function getTotalDepense(): float
    {
        return array_reduce(
            $this->commandes->toArray(),
            fn ($total, $commande) => $total + $commande->getPrixTotal(),
            0
        );
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }
}

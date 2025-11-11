<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'client')]
    private Collection $commandes;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'client')]
    private Collection $avis;

    public function __construct()
    {
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
            // set the owning side to null (unless already changed)
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

    // Renvoie la dernière commande passée (par date)
    public function getDerniereCommande(): ?Commande
    {
        if ($this->commandes->isEmpty()) {
            return null;
        }

        $commandesTriees = $this->commandes->toArray();
        usort($commandesTriees, fn($a, $b) => $b->getDateCommande() <=> $a->getDateCommande());

        return $commandesTriees[0];
    }

    // Calcule le montant total dépensé par le client
    public function getTotalDepense(): float
    {
        $total = 0;
        foreach ($this->commandes as $commande) {
            $total += $commande->getTotal();
        }
        return $total;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setClient($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getClient() === $this) {
                $avi->setClient(null);
            }
        }

        return $this;
    }
}

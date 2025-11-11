<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande')]
final class OrderController extends AbstractController
{
    #[Route('/passer', name: 'commande_passer')]
    public function passerCommande(EntityManagerInterface $em, PanierRepository $repo): Response
    {
        $client = $this->getUser();
        $panier = $repo->findOneBy(['client' => $client]);

        if (!$panier || count($panier->getItems()) === 0) {
            return $this->redirectToRoute('panier_index');
        }

        $commande = new Commande();
        $commande->setClient($client);
        $commande->setDateCommande(new \DateTime());
        $commande->setTotal($panier->calculerTotal());
        $commande->setStatut('En attente');

        $em->persist($commande);
        $em->remove($panier); // vider le panier après commande
        $em->flush();

        return $this->render('commande/success.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/', name: 'commande_index')]
    public function index(): Response
    {
        $client = $this->getUser();
        //$commandes = $client->getCommandes();
        return $this->render('commande/index.html.twig', [
            //'commandes' => $commandes,
        ]);
    }
}

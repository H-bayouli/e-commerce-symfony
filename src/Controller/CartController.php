<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\LignePanier;
use App\Entity\Produit;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
final class CartController extends AbstractController
{
    #[Route('/', name: 'panier_index')]
    public function index(PanierRepository $repo): Response
    {
        $client = $this->getUser();
        $panier = $repo->findOneBy(['client' => $client]);

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/add/{id}', name: 'panier_add')]
    public function add(Produit $produit, EntityManagerInterface $em, PanierRepository $repo): Response
    {
        $client = $this->getUser();
        $panier = $repo->findOneBy(['client' => $client]) ?? (new Panier())->setClient($client);

        $ligne = new LignePanier();
        $ligne->setProduit($produit);
        $ligne->setQuantite(1);
        $panier->addItem($ligne);

        $em->persist($panier);
        $em->flush();

        return $this->redirectToRoute('panier_index');
    }

    #[Route('/remove/{id}', name: 'panier_remove')]
    public function remove(LignePanier $ligne, EntityManagerInterface $em): Response
    {
        $em->remove($ligne);
        $em->flush();
        return $this->redirectToRoute('panier_index');
    }
}

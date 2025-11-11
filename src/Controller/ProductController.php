<?php

namespace App\Controller;


use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/', name: 'produit_index')]
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        return $this->render('product/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/new', name: 'produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $produit = new Produit();
            $produit->setNom($request->get('nom'));
            $produit->setDescription($request->get('description'));
            $produit->setPrix($request->get('prix'));
            $produit->setStock($request->get('stock'));

            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('product/new.html.twig');
    }

    #[Route('/{id}/delete', name: 'produit_delete')]
    public function delete(Produit $produit, EntityManagerInterface $em): Response
    {
        $em->remove($produit);
        $em->flush();

        return $this->redirectToRoute('produit_index');
    }
}

<?php

// src/Controller/AvisController.php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    #[Route('/avis/add/{id}', name: 'app_avis_add', methods: ['POST'])]
    public function add(
        Produit $produit,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $avis = new Avis();
        $avis->setProduit($produit);
        $avis->setNote((int) $request->request->get('note'));
        $avis->setCommentaire($request->request->get('commentaire'));

        // if client is logged in
        if ($this->getUser()) {
            $avis->setClient($this->getUser());
        }

        $em->persist($avis);
        $em->flush();

        return $this->redirectToRoute('app_produit_show', [
            'id' => $produit->getId()
        ]);
    }
}

?>
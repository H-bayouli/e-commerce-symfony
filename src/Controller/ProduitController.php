<?php

namespace App\Controller;

use App\Entity\AddProductHistory;
use App\Entity\Produit;
use App\Form\AddProductHistoryType;
use App\Form\ProduitType;
use App\Form\ProduitUpdateType;
use App\Repository\AddProductHistoryRepository;
use App\Repository\ProduitRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/editor/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

/////new//////

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image=$form->get('image')->getData();

            if($image){
                $originalName=pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME);

                $safeFileName=$slugger->slug($originalName);

                $newFileName = $safeFileName.'-'.uniqid().'.'.$image->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('image_dir'),
                        $newFileName
                    );

                }catch(FileException $exception){

                }

                $produit->setImage($newFileName);
            }
            $entityManager->persist($produit);
            $entityManager->flush();

            $stockHistory= new AddProductHistory();

            $stockHistory->setQte($produit->getStock());

            $stockHistory->setProduit($produit);

            $stockHistory->setCreatedAt(new DateTimeImmutable());

            $entityManager->persist($stockHistory);

            $this->addFlash('success','votre produit a été ajouté');

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }


///////show///////////
    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

//////edit/////

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitUpdateType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image=$form->get('image')->getData();

            if($image){
                $originalName=pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME);

                $safeFileName=$slugger->slug($originalName);

                $newFileName = $safeFileName.'-'.uniqid().'.'.$image->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('image_dir'),
                        $newFileName
                    );

                }catch(FileException $exception){

                }

                $produit->setImage($newFileName);
            }

            $entityManager->flush();

            $this->addFlash('success','votre produit a été modifié');

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

////delete//////

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();

            $this->addFlash('danger','votre produit a été supprimé');
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add/produit/{id}/stock', name: 'app_produit_stock_add', methods: ['POST','GET'])]
    public function addStock($id, EntityManagerInterface $entityManager, Request $request, ProduitRepository $produitRepository): Response
    {
        $addStock= new AddProductHistory();
        $form = $this->createForm(AddProductHistoryType::class,$addStock);

        $form->handleRequest($request);

        $produit= $produitRepository->find($id);

        if($form->isSubmitted() && $form->isValid())
        {
            if($addStock->getQte()>0)
            {
                $newQte= $produit->getStock() + $addStock->getQte();
                $produit->setStock($newQte);
                $addStock->setProduit($produit);
                $addStock->setCreatedAt(new DateTimeImmutable());

                $entityManager->persist($addStock);
                $entityManager->flush();

                $this->addFlash('success','le stock de votre produit a été modifié');
                return $this->redirectToRoute('app_produit_index');
            }else{
                $this->addFlash('danger','le stock de votre produit pas être inferieur à 0');
                return $this->redirectToRoute('app_produit_stock_add',['id'=>$produit->getId()]);
            }
        }

        return $this->render('produit/addStock.html.twig',
            ['form'=> $form->createView(), 'produit'=>$produit]
        );
    }

/////product add history///////

    #[Route('/add/produit/{id}/stock/history', name: 'app_produit_stock_add_history', methods: ['GET'])]
    public function productAddHistory($id, ProduitRepository $produitRepository, AddProductHistoryRepository $addProductHistory):Response
    {

        $produit=$produitRepository->find($id);
        $produitHistory=$addProductHistory->findBy(['produit'=>$produit],['createdAt'=>"DESC"]);
        
        return $this->render('produit/showaddStockHistory.html.twig',
        ["productadded"=> $produitHistory]
        );
    }
}

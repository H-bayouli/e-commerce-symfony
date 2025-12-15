<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\CategoryRepository;
use App\Repository\ProduitRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home', methods:['GET'])]
    public function index(ProduitRepository $produitRepository,CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $search= $produitRepository->SearchEngine('');

        $data=$produitRepository->findBy([],['id'=>'DESC']);
        $products=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            10
        );
        return $this->render('home/index.html.twig', [
            'produits'=>$products,
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/home/produit/{id}/show', name: 'app_product_show', methods:['GET'])]
    public function show(Produit $produit, ProduitRepository $produitRepository,CategoryRepository $categoryRepository): Response
    {
        $lastProducts=$produitRepository->findBy([],['id'=>'DESC'],5);
        return $this->render('home/show.html.twig', [
            'produit'=>$produit,
            'dernierproduits'=>$lastProducts,
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/home/produit/subcategory/{id}/filter', name: 'app_product_filter', methods:['GET'])]
    public function filter($id, SubCategoryRepository $subCategoryRepository,CategoryRepository $categoryRepository): Response
    {
        $products = $subCategoryRepository->find($id)->getProduits();
        $subCategory = $subCategoryRepository->find($id);
        return $this->render('home/filter.html.twig',[
            'products'=>$products,
            'subcategory'=>$subCategory,
            'categories'=>$categoryRepository->findAll()
        ]);
    }


}

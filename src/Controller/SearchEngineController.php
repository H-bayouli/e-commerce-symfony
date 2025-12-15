<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine',methods:['GET'])]
    public function index(Request $request,ProduitRepository $produitRepository): Response
    {
        if($request->isMethod('GET')){
            $dta=$request->query->all();
            $word=$dta['word'];
            $results= $produitRepository->SearchEngine($word);
        }
        return $this->render('search_engine/index.html.twig', [
            'products' => $results,
            'word'=>$word
        ]);
    }
}

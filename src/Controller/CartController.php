<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\LignePanier;
use App\Entity\Produit;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
final class CartController extends AbstractController
{


    public function __construct(private readonly ProduitRepository $produit_repository){
    }

    #[Route('/', name: 'panier_index', methods: ['GET'])]
    public function index(SessionInterface $session, PanierService $panier): Response
    {
        $data= $panier->getPanier($session);

        //dd($data['panier']);

        /*
        $prdouitsPanier=$data['panier'];
        $produits=[];

        foreach($prdouitsPanier as $value){

        }
        */

        //dd($cartwhitData);
        return $this->render('panier/index.html.twig', [
            'items'=>$data['panier'],
            'total'=>$data['total']
        ]);
    }



    #[Route('/add/{id}', name: 'panier_add', methods: ['GET'])]
    public function add(int $id, SessionInterface $session): Response
    {

        $cart = $session->get('panier',[]);
        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id]=1;
        }
        $session->set('panier',$cart);

        return $this->redirectToRoute('panier_index');
    }



    #[Route('/remove/{id}', name: 'panier_remove')]
    public function removeTocart($id, SessionInterface $session): Response
    {
        $cart = $session->get('panier',[]);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        $session->set('panier',$cart);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/remove', name: 'panier_remove_all')]
    public function remove(SessionInterface $session): Response
    {

        $session->set('cart',[]);
        return $this->redirectToRoute('panier_index');
    }
}

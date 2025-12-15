<?php

    namespace App\service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

    class PanierService
    {
        public function __construct(private readonly ProduitRepository $produit_repository){
        }

        public function getPanier(SessionInterface $session):array
        {
            $cart= $session->get('panier',[]);
            $cartwhitData=[];
            foreach($cart as $id=>$quantity){
                $cartwhitData[] = [
                    'produit'=>$this->produit_repository->find($id),
                    'quantity'=>$quantity
                ];
            }

            $total= array_sum(array_map(function ($item){
                return $item['produit']->getPrix()* $item['quantity'];
            },$cartwhitData));

            return [
                'panier'=>$cartwhitData,
                'total'=>$total
            ];
        }
    }

?>
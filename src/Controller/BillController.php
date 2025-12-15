<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BillController extends AbstractController
{
    #[Route('/editor/bill', name: 'app_bill')]
    public function index($id,CommandeRepository $commandeRepository): Response
    {
        $commande= $commandeRepository->find($id);

        $pdf=new Options();
        $pdf->set('defaultFont','Arial');
        $dompdf=new Dompdf($pdf);

        $html=$this->renderView('bill/index.html.twig', [
            'commande' => $commande
        ]);

        $dompdf->loadHtml($html);

        $dompdf->render();

        $dompdf->stream('bill-'.$commande->getId().'.pdf',[
            'Attachement'=>false
        ]);
        return  new Response('',200,[
            'content-type'=>'application/pdf'
        ]);
    }
}

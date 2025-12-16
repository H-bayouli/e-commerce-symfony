<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe_success')]
    public function success(Panier $panier,SessionInterface $session): Response
    {
        $session->set('panier',[]);
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/stripe', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/stripe/notify',name: 'app_stripe_notify')]
    public function stripeNotify($orderId,Request $request,CommandeRepository $commanderepository, EntityManagerInterface $en,Commande $commande):Response
    {
        $order = $commanderepository->find($orderId);

        $panierPrix= $commande->getTotalPrice();

        $stripeTotalAmount= $paymentIntent->amount/100;

        if($panierPrix==$stripeTotalAmount){
            $order->setIsPaymentCompleted(1);
            $en->flush();
        }
        
        Stripe::setApiKey($_SERVER['STRIPE_SECRET']);

        $endpoint_secret = '';

        $payload = $request->getContent();

        $sig_header = $request->headers->get('stripe-signature');

        $event= null;

        try{
            $event= \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        }catch(\UnexpectedValueException $e){
            return new Response('payload invalide', 400);
        }catch(\Stripe\Exception\SignatureVerificationException $e){
            return new Response('Signature invalide');
        }

        switch($event->type){
            case 'payment_intent_succeded':
                $paymentIntent= $event->data->object;

                $fileName= 'stripe-details-'.uniqid().'txt';

                file_put_contents($fileName,$paymentIntent);
                break;
            case 'payment_method_attached':
                $paymentMethod= $event->data->object;
                break;
            default:
                break;
        }

        return new Response('eveneemnt reçu',200);


    }
}

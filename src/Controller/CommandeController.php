<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Commande;
use App\Entity\CommanderProduits;
use App\Entity\Panier;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\service\PanierService;
use App\service\StripePayment;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    public function __construct(private MailerInterface $mailer){}


    #[Route('/editor/commande/update', name: 'app_commande')]
    public function index(Request $request, 
        SessionInterface $session, 
        ProduitRepository $produit_repository,
        CommandeRepository $commandeRepository, 
        EntityManagerInterface $entityManager,
        PanierService $panierserivce): Response
    {
        $data= $panierserivce->getPanier($session);

        $commande= new Commande();
        $form=$this->createForm(CommandeType::class,$commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){   
            if(!empty($data['total'])){
                $PrixTotal=$data['total']*$commande->getCity()->getShippingCost();
                $commande->setTotal($PrixTotal);
                $commande->setCreatedAt(new DateTimeImmutable());
                //$commande->setIsPaymentCompleted();
                $entityManager->persist($commande);
                $entityManager->flush();

                foreach($data['panier'] as $value){
                    $commandeProduit= new CommanderProduits();
                    $commandeProduit->setCommande($commande);
                    
                    $commandeProduit->setProduit($value['produit']);
                    $commandeProduit->setQte($value['quantity']);
                    $entityManager->persist($commandeProduit);
                    $entityManager->flush();
                }
                if($commande->isPayOnDelivery()){
                    $session->set('cart',[]);

                    $html=$this->renderView('mail/orderconfirrm.html.twig',[
                        'commande'=>$commande
                    ]);

                    $email=(new Email())
                    ->from('myshop@gmail.com')
                    ->to($commande->getEmail())
                    ->subject('confirmation de reception de la commande')
                    ->html($html);

                    $this->mailer->send($email);
                    
                    return $this->redirectToRoute('commande_message');
                }
                
                $shippingCost=$commande->getCity()->getShippingCost();

                $payment=new StripePayment();

                $payment->startPayment($data,$shippingCost,$commande->getId());

                $stripeRedirectUrl= $payment->getStripeRedirectUrl();

                return $this->redirect($stripeRedirectUrl);
            }

        }
        return $this->render('commande/index.html.twig', [
            'form'=>$form->createView(),
            'total'=>$data['total']
        ]);
    }

    #[Route('/editor/commande/create', name: 'app_commande_create')]
    public function createCommande(
        Request $request, 
        SessionInterface $session, 
        ProduitRepository $produit_repository,
        CommandeRepository $commandeRepository, 
        EntityManagerInterface $entityManager,
        PanierService $panierserivce
    ):Response
    {
                $data= $panierserivce->getPanier($session);

        $commande= new Commande();
        $form=$this->createForm(CommandeType::class,$commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){   
            if(!empty($data['total'])){
                $PrixTotal=$data['total']*$commande->getCity()->getShippingCost();
                $commande->setTotal($PrixTotal);
                $commande->setCreatedAt(new DateTimeImmutable());
                //$commande->setIsPaymentCompleted();
                $entityManager->persist($commande);
                $entityManager->flush();

                foreach($data['panier'] as $value){
                    $commandeProduit= new CommanderProduits();
                    $commandeProduit->setCommande($commande);
                    
                    $commandeProduit->setProduit($value['produit']);
                    $commandeProduit->setQte($value['quantity']);
                    $entityManager->persist($commandeProduit);
                    $entityManager->flush();
                }
                if($commande->isPayOnDelivery()){
                    $session->set('cart',[]);

                    $html=$this->renderView('mail/orderconfirrm.html.twig',[
                        'commande'=>$commande
                    ]);

                    $email=(new Email())
                    ->from('myshop@gmail.com')
                    ->to($commande->getEmail())
                    ->subject('confirmation de reception de la commande')
                    ->html($html);

                    $this->mailer->send($email);
                    
                    return $this->redirectToRoute('commande_message');
                }
                
                $shippingCost=$commande->getCity()->getShippingCost();

                $payment=new StripePayment();

                $payment->startPayment($data,$shippingCost,$commande->getId());

                $stripeRedirectUrl= $payment->getStripeRedirectUrl();

                return $this->redirect($stripeRedirectUrl);
            }

        }
        return $this->render('commande/index.html.twig', [
            'form'=>$form->createView(),
            'total'=>$data['total']
        ]);
    }



    #[Route('/editor/commande/{type}', name: 'app_commande')]
    public function getAllOrder($type,CommandeRepository $commandeRepository, Request $request, PaginatorInterface $paginator):Response
    {
        if($type=='livrée'){
            $data=$commandeRepository->findBy(['isCompleted'=>1],['id'=>'DESC']);
        }elseif($type=='pay-onstripe-not-delivered'){
            $data=$commandeRepository->findBy(['isCompleted'=>null,'payOnDelivery'=>0,'isPaymentCompleted'=>1],['id'=>'DESC']);
        }elseif($type=='pay-onstripe-is-delivered'){
            $data=$commandeRepository->findBy(['isCompleted'=>1,'payOnDelivery'=>1,'isPaymentCompleted'=>1],['id'=>'DESC']);
        }elseif($type=='valider_commande'){
            $data=$commandeRepository->findBy([],['id'=>'DESC']);
        }
        $data=$commandeRepository->findBy([],['id'=>'DESC']);
        $commandes=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            10
        );
        return $this->render('commande/commande.html.twig',[
            'commandes'=>$commandes
        ]);
    }

    #[Route('/editor/commande/{id}/is_completed/update', name: 'app_commande_iscompleted_update')]
    public function isCompletedUpdate($id,CommandeRepository $commandeRepository, EntityManagerInterface $en,Request $request):Response
    {
        $commande=$commandeRepository->find($id);
        $commande->setIsCompleted(true);
        $en->flush();
        $this->addFlash('success','commande marquée comme livrée');
        return $this->redirect($request->headers->get('referer'));
    }


    #[Route('/editor/commande/{id}/remove', name: 'app_commande_remove')]
    public function removeOrder(Commande $commande, EntityManagerInterface $en):Response
    {
        $en->remove($commande);
        $en->flush();
        $this->addFlash('danger','commande a été supprimée');
        return $this->redirectToRoute('app_commande');
    }

    
    #[Route("/commande_message",name: 'commande_message')]
    public function commandeMessage():Response
    {
        return $this->render('commande/commande_message.html.twig');
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function CityShippingCost(City $city):Response
    {
        $cityChippingPrice= $city->getShippingCost();

        return new Response(json_encode(['status'=>200,'message'=>'on','content'=>$cityChippingPrice]));
    }
}

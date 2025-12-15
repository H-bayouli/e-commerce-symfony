<?php

namespace App\service;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePayment
{
    private $redirectUrl;

    public function __construct()
    {
        // Access the Stripe secret key from the environment
        Stripe::setApiKey(getenv('STRIPE_SECRET')); // Assuming STRIPE_SECRET is set in your environment variables
        Stripe::setApiVersion('2020-08-27'); // Replace with your actual Stripe API version
    }

    public function startPayment($panier, $shippingCost, $orderId)
    {
        $cartProducts = $panier['panier']; // Assuming 'panier' contains the cart products

        // Initialize products with shipping costs
        $products = [
            [
                'qte' => 1,
                'prix' => $shippingCost,
                'name' => "frais de livraison"
            ]
        ];

        // Loop through the cart products and prepare them for Stripe
        foreach ($cartProducts as $value) {
            $productItem = [];
            $productItem['name'] = $value['product']->getName();
            $productItem['prix'] = $value['product']->getPrix();
            $productItem['qte'] = $value['quantity'];
            $products[] = $productItem;
        }

        // Create a new Stripe session
        $session = Session::create([
            'line_items' => array_map(fn(array $produit) => [
                'quantity' => $produit['qte'],
                'price_data' => [
                    'currency' => 'TND', // Correct currency code for Tunisia
                    'product_data' => [
                        'name' => $produit['name']
                    ],
                    'unit_amount' => $produit['prix'] * 100 // Amount in cents
                ],
            ], $products),
            'mode' => 'payment',
            'cancel_url' => 'http://localhost:8000/pay/cancel',
            'success_url' => 'http://localhost:8000/pay/success',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR', 'TN']
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'commandeId' => $orderId
                ]
            ]
        ]);

        // Set the redirect URL for later use
        $this->redirectUrl = $session->url;
    }

    public function getStripeRedirectUrl()
    {
        return $this->redirectUrl; // Return the redirect URL
    }
}

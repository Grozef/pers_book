<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Charge;

class StripeController extends AbstractController
{
    /**
     * Process a payment using Stripe.
     *
     * @Route("/stripe/charge", name="stripe_charge", methods={"POST"})
     */
    public function charge(): JsonResponse
    {
        // .env.local
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            // Create a charge
            $charge = Charge::create([
                'amount' => 1000, // Amount in cents
                'currency' => 'usd',
                'source' => 'tok_visa', // This should be replaced with the actual token from your frontend
                'description' => 'Example charge',
            ]);

            // Return a success response
            return new JsonResponse(['status' => 'Payment successful', 'charge' => $charge]);
        } catch (\Exception $e) {
            // Return an error response
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}

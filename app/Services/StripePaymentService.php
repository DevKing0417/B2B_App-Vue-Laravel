<?php

namespace App\Services;

use App\Models\Donation;
use Stripe\Stripe;
use Stripe\Charge;

class StripePaymentService implements PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function processPayment(Donation $donation, string $paymentToken): object
    {
        try {
            $charge = Charge::create([
                'amount' => $donation->amount * 100, // Convert to cents
                'currency' => 'usd',
                'source' => $paymentToken,
                'description' => "Donation to campaign: {$donation->campaign->title}",
                'metadata' => [
                    'donation_id' => $donation->id,
                    'campaign_id' => $donation->campaign_id,
                    'user_id' => $donation->user_id,
                ],
            ]);

            return $charge;
        } catch (\Stripe\Exception\CardException $e) {
            throw new \Exception('Card error: ' . $e->getMessage());
        } catch (\Stripe\Exception\RateLimitException $e) {
            throw new \Exception('Rate limit error: ' . $e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            throw new \Exception('Invalid request: ' . $e->getMessage());
        } catch (\Stripe\Exception\AuthenticationException $e) {
            throw new \Exception('Authentication error: ' . $e->getMessage());
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            throw new \Exception('Network error: ' . $e->getMessage());
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception('Stripe API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Payment processing error: ' . $e->getMessage());
        }
    }
} 
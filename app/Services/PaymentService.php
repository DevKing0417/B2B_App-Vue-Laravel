<?php

namespace App\Services;

use App\Models\Donation;

interface PaymentService
{
    /**
     * Process a payment for a donation
     *
     * @param Donation $donation
     * @param string $paymentToken
     * @return object
     * @throws \Exception
     */
    public function processPayment(Donation $donation, string $paymentToken): object;
} 
<?php

// app/Services/PaymentGatewayService.php
namespace App\Services;

use App\Models\Transaction;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class PaymentGatewayService
{
    public function processPayment(Transaction $transaction, $gatewayName)
    {
        $gateway = PaymentGateway::where('name', $gatewayName)->where('is_active', true)->first();
        
        if (! $gateway) {
            throw new \Exception('Payment gateway not found or inactive.');
        }
        
        $config = json_decode($gateway->config, true);
        
        switch ($gatewayName) {
            case 'stripe':
                return $this->processStripePayment($transaction, $config);
            case 'razorpay':
                return $this->processRazorpayPayment($transaction, $config);
            default:
                throw new \Exception('Unsupported payment gateway.');
        }
    }
    
    private function processStripePayment(Transaction $transaction, $config)
    {
        // Implement Stripe payment processing
        // This is a simplified example
        try {
            $response = Http::withToken($config['secret_key'])
                ->post('https://api.stripe.com/v1/payment_intents', [
                    'amount' => $transaction->amount * 100, // Stripe uses cents
                    'currency' => 'inr',
                    'description' => 'Payment for Student Fees',
                    'metadata' => [
                        'transaction_id' => $transaction->id,
                        'student_id' => $transaction->student_id,
                    ],
                ]);
            
            if ($response->successful()) {
                $paymentIntent = $response->json();
                
                // Update transaction with payment intent ID
                $transaction->update([
                    'reference_id' => $paymentIntent['id'],
                    'status' => 'pending',
                ]);
                
                return [
                    'success' => true,
                    'client_secret' => $paymentIntent['client_secret'],
                    'payment_intent_id' => $paymentIntent['id'],
                ];
            } else {
                throw new \Exception('Stripe payment failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            throw new \Exception('Stripe payment error: ' . $e->getMessage());
        }
    }
    
    private function processRazorpayPayment(Transaction $transaction, $config)
    {
        // Implement Razorpay payment processing
        // This is a simplified example
        try {
            $response = Http::withBasicAuth($config['key_id'], $config['key_secret'])
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $transaction->amount * 100, // Razorpay uses paise
                    'currency' => 'INR',
                    'receipt' => 'receipt_' . $transaction->id,
                    'notes' => [
                        'transaction_id' => $transaction->id,
                        'student_id' => $transaction->student_id,
                    ],
                ]);
            
            if ($response->successful()) {
                $order = $response->json();
                
                // Update transaction with order ID
                $transaction->update([
                    'reference_id' => $order['id'],
                    'status' => 'pending',
                ]);
                
                return [
                    'success' => true,
                    'order_id' => $order['id'],
                    'razorpay_key' => $config['key_id'],
                ];
            } else {
                throw new \Exception('Razorpay payment failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            throw new \Exception('Razorpay payment error: ' . $e->getMessage());
        }
    }
    
    public function verifyPayment($gatewayName, $paymentData)
    {
        switch ($gatewayName) {
            case 'stripe':
                return $this->verifyStripePayment($paymentData);
            case 'razorpay':
                return $this->verifyRazorpayPayment($paymentData);
            default:
                throw new \Exception('Unsupported payment gateway.');
        }
    }
    
    private function verifyStripePayment($paymentData)
    {
        // Implement Stripe payment verification
        // This is a simplified example
        try {
            $gateway = PaymentGateway::where('name', 'stripe')->where('is_active', true)->first();
            $config = json_decode($gateway->config, true);
            
            $response = Http::withToken($config['secret_key'])
                ->get("https://api.stripe.com/v1/payment_intents/{$paymentData['payment_intent_id']}");
            
            if ($response->successful()) {
                $paymentIntent = $response->json();
                
                if ($paymentIntent['status'] === 'succeeded') {
                    $transaction = Transaction::where('reference_id', $paymentIntent['id'])->first();
                    
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'completed',
                        ]);
                        
                        return [
                            'success' => true,
                            'transaction' => $transaction,
                        ];
                    }
                }
            }
            
            return [
                'success' => false,
                'message' => 'Payment verification failed.',
            ];
        } catch (\Exception $e) {
            throw new \Exception('Stripe payment verification error: ' . $e->getMessage());
        }
    }
    
    private function verifyRazorpayPayment($paymentData)
    {
        // Implement Razorpay payment verification
        // This is a simplified example
        try {
            $gateway = PaymentGateway::where('name', 'razorpay')->where('is_active', true)->first();
            $config = json_decode($gateway->config, true);
            
            $generatedSignature = hash_hmac('sha256', $paymentData['razorpay_order_id'] . "|" . $paymentData['razorpay_payment_id'], $config['key_secret']);
            
            if ($generatedSignature === $paymentData['razorpay_signature']) {
                $transaction = Transaction::where('reference_id', $paymentData['razorpay_order_id'])->first();
                
                if ($transaction) {
                    $transaction->update([
                        'status' => 'completed',
                    ]);
                    
                    return [
                        'success' => true,
                        'transaction' => $transaction,
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Payment verification failed.',
            ];
        } catch (\Exception $e) {
            throw new \Exception('Razorpay payment verification error: ' . $e->getMessage());
        }
    }
}
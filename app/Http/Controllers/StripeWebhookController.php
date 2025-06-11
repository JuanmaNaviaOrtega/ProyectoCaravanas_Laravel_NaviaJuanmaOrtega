<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook.secret')
            );
        } catch (\UnexpectedValueException $e) {
           
            Log::error('Stripe webhook invalid payload: ' . $e->getMessage());
            return response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            
            Log::error('Stripe webhook invalid signature: ' . $e->getMessage());
            return response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

       
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;
            case 'charge.refunded':
                $charge = $event->data->object;
                $this->handleChargeRefunded($charge);
                break;
            default:
                Log::info('Received unhandled event type: ' . $event->type);
        }

        return response('Webhook Handled', Response::HTTP_OK);
    }

    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Actualizar la reserva como pagada
        $reserva = \App\Models\Reserva::where('transaccion_id', $paymentIntent->id)->first();
        
        if ($reserva) {
            $reserva->update(['estado' => 'confirmada']);
            
            // Enviar email de confirmación
            
            
            Log::info('Reserva confirmada: ' . $reserva->id);
        }
    }

    protected function handlePaymentIntentFailed($paymentIntent)
    {
        // Marcar reserva como fallida
        $reserva = \App\Models\Reserva::where('transaccion_id', $paymentIntent->id)->first();
        
        if ($reserva) {
            $reserva->update(['estado' => 'fallida']);
            Log::warning('Pago fallido para reserva: ' . $reserva->id);
        }
    }

    protected function handleChargeRefunded($charge)
    {
        // Manejar reembolsos
        $reserva = \App\Models\Reserva::where('transaccion_id', $charge->payment_intent)->first();
        
        if ($reserva) {
            $reserva->update(['estado' => 'reembolsada']);
            Log::info('Reserva reembolsada: ' . $reserva->id);
        }
    }
}

<?php

// app/Listeners/LogPaymentActivity.php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use App\Events\RefundProcessed;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogPaymentActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $action = '';
        $description = '';
        
        if ($event instanceof PaymentProcessed) {
            $action = 'payment';
            $description = "Processed payment of {$event->transaction->amount} for student ID: {$event->transaction->student_id}";
            
            // Log details of fees paid
            $paymentDetails = [];
            foreach ($event->transaction->payments as $payment) {
                $fee = $payment->studentFee;
                $paymentDetails[] = "{$fee->feeStructure->feeType->name}: {$payment->amount}";
            }
            
            if (!empty($paymentDetails)) {
                $description .= " (Fees: " . implode(', ', $paymentDetails) . ")";
            }
        } elseif ($event instanceof RefundProcessed) {
            $action = 'refund';
            $refund = $event->transaction->refund;
            $description = "Processed refund of {$event->transaction->amount} for student ID: {$event->transaction->student_id}";
            
            if ($refund) {
                $description .= " (Reason: {$refund->reason})";
            }
        }
        
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($event->transaction),
            'model_id' => $event->transaction->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
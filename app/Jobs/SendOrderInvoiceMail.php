<?php

namespace App\Jobs;

use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderInvoiceMail implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(public int $orderId) {}

    public function uniqueId(): string
    {
        return 'order-invoice-'.$this->orderId;
    }

    public function handle(): void
    {
        $order = Order::find($this->orderId);

        if (! $order) {
            return;
        }

        $meta = $order->payment_meta ?? [];
        if (! empty($meta['invoice_emailed_at'])) {
            return;
        }

        $billingEmail = $order->billing_address['email'] ?? null;
        if (! $billingEmail) {
            $billingEmail = $order->user?->email;
        }

        if (! $billingEmail) {
            return;
        }

        Mail::to($billingEmail)->send(new OrderInvoiceMail($order->fresh()));

        $order->refresh();
        $latestMeta = $order->payment_meta ?? [];
        $order->update([
            'payment_meta' => array_merge($latestMeta, [
                'invoice_queued_at' => $meta['invoice_queued_at'] ?? now()->toDateTimeString(),
                'invoice_emailed_at' => now()->toDateTimeString(),
            ]),
        ]);
    }

    /**
     * All three tries failed (bad SMTP credentials, provider outage, ...).
     *
     * Log it so the failure is visible in laravel.log instead of dying silently
     * in failed_jobs, and clear the one-shot guard: every checkout path skips
     * queueing once `invoice_queued_at` is set, so without this an order whose
     * first attempt failed would never get an invoice email again - not even
     * after the mail configuration is fixed and the checkout is retried.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Order invoice mail failed permanently.', [
            'order_id' => $this->orderId,
            'error' => $exception->getMessage(),
        ]);

        $order = Order::find($this->orderId);

        if (! $order) {
            return;
        }

        $meta = $order->payment_meta ?? [];
        if (! empty($meta['invoice_emailed_at']) || empty($meta['invoice_queued_at'])) {
            return;
        }

        unset($meta['invoice_queued_at']);
        $order->update(['payment_meta' => $meta]);
    }
}

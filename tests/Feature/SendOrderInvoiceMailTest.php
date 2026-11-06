<?php

use App\Jobs\SendOrderInvoiceMail;
use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

function invoiceMailTestOrder(array $attributes = []): Order
{
    $user = User::factory()->create();

    return Order::create(array_merge([
        'user_id' => $user->id,
        'status' => 'processing',
        'payment_method' => 'cod',
        'payment_status' => 'unpaid',
        'total' => 1500.00,
    ], $attributes));
}

it('sends the invoice to the billing email and stamps the order meta', function () {
    Mail::fake();

    $order = invoiceMailTestOrder([
        'billing_address' => ['email' => 'buyer@example.com'],
        'payment_meta' => ['invoice_queued_at' => now()->toDateTimeString()],
    ]);

    (new SendOrderInvoiceMail($order->id))->handle();

    Mail::assertSent(OrderInvoiceMail::class, fn ($mail) => $mail->hasTo('buyer@example.com'));

    $meta = $order->fresh()->payment_meta;
    expect($meta['invoice_emailed_at'])->not->toBeNull()
        ->and($meta['invoice_queued_at'])->not->toBeNull();
});

it('falls back to the account email when billing has none', function () {
    Mail::fake();

    $user = User::factory()->create(['email' => 'account-owner@example.com']);
    $order = Order::create([
        'user_id' => $user->id,
        'status' => 'processing',
        'payment_method' => 'cod',
        'payment_status' => 'unpaid',
        'total' => 999.00,
        'payment_meta' => ['invoice_queued_at' => now()->toDateTimeString()],
    ]);

    (new SendOrderInvoiceMail($order->id))->handle();

    Mail::assertSent(OrderInvoiceMail::class, fn ($mail) => $mail->hasTo('account-owner@example.com'));
});

it('unblocks re-queueing after a permanent failure', function () {
    // The checkout paths skip queueing while invoice_queued_at is set, so a
    // permanently failed job must clear it or the order never emails again.
    $order = invoiceMailTestOrder([
        'payment_meta' => ['invoice_queued_at' => now()->toDateTimeString()],
    ]);

    (new SendOrderInvoiceMail($order->id))->failed(new RuntimeException('SMTP auth failed'));

    expect($order->fresh()->payment_meta)->not->toHaveKey('invoice_queued_at');
});

it('keeps the emailed stamp when a late failure arrives', function () {
    $order = invoiceMailTestOrder([
        'payment_meta' => [
            'invoice_queued_at' => now()->subMinute()->toDateTimeString(),
            'invoice_emailed_at' => now()->toDateTimeString(),
        ],
    ]);

    (new SendOrderInvoiceMail($order->id))->failed(new RuntimeException('too late'));

    $meta = $order->fresh()->payment_meta;
    expect($meta['invoice_emailed_at'])->not->toBeNull()
        ->and($meta['invoice_queued_at'])->not->toBeNull();
});

it('links to the react storefront order page when the blade route is disabled', function () {
    // routes/web.php comments the storefront out; the order page now lives in
    // the React app, so the link must fall back instead of throwing while the
    // email renders.
    $order = invoiceMailTestOrder();

    $base = rtrim((string) (config('storefront.url') ?: url('/')), '/');

    expect(Route::has('orders.show'))->toBeFalse()
        ->and($order->viewUrl())->toBe($base . '/orders/' . $order->id);
});

it('prefers the blade order route when it is registered', function () {
    Route::get('/orders/{order}', fn () => response('ok'))->name('orders.show');
    // A route named after it was added to the collection needs the same
    // lookup refresh the framework performs during a real request.
    Route::getRoutes()->refreshNameLookups();

    $order = invoiceMailTestOrder();

    expect(Route::has('orders.show'))->toBeTrue()
        ->and($order->viewUrl())->toBe(route('orders.show', $order));
});

it('renders the invoice email with the storefront order link', function () {
    // The full mailable - PDF attachment included - used to crash with
    // RouteNotFoundException on route('orders.show'), so the queued job
    // retried three times and the customer never got the invoice.
    $order = invoiceMailTestOrder([
        'billing_address' => ['email' => 'buyer@example.com'],
    ]);

    $rendered = (new OrderInvoiceMail($order))->render();

    expect($rendered)->toContain('/orders/' . $order->id);
});

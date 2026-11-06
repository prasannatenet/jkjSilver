<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

use function Pest\Laravel\postJson;

it('sends a welcome email to a new user on API registration', function () {
    Mail::fake();

    // The React storefront registers through the token API; the old Blade
    // register route was removed along with the storefront, so the welcome
    // mail has to fire from Api\AuthController instead.
    postJson('/api/v1/auth/register', [
        'name' => 'Riya Sharma',
        'email' => 'riya.sharma@example.com',
        'phone' => '9812345678',
        'password' => 'Password#123',
        'password_confirmation' => 'Password#123',
    ])->assertCreated();

    Mail::assertSent(\App\Mail\WelcomeUserMail::class, function ($mail) {
        return $mail->hasTo('riya.sharma@example.com')
            && $mail->user->name === 'Riya Sharma';
    });
});

it('renders the welcome email view with the user name and store name', function () {
    $user = User::factory()->create(['name' => 'Priya Patel']);

    $mail = new \App\Mail\WelcomeUserMail($user);

    // Render the mailable without actually dispatching mail.
    $rendered = $mail->render();

    expect($rendered)->toContain('Priya Patel')
        ->and($mail->subject)->toContain(config('app.name'));
});

it('sends a return request email to the order owner', function () {
    Mail::fake();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $order = Order::create([
        'user_id' => $user->id,
        'status' => 'delivered',
        'total' => 1000.00,
    ]);

    $item = new OrderItem([
        'order_id' => $order->id,
        'product_name' => 'Cotton Saree',
        'sku' => 'CS-001',
        'unit_price' => 500.00,
        'quantity' => 2,
        'line_total' => 1000.00,
    ]);
    $item->order_id = $order->id;
    $item->save();

    // Returns are requested through the token API now that the storefront is
    // gone; the response reports whether the notification email went out.
    $response = postJson(route('api.orders.return-requests.store', $order), [
        'reason' => 'Wrong size',
        'payout_method' => 'upi',
        'upi_id' => 'riya@upi',
        'items' => [['order_item_id' => $item->id, 'quantity' => 1]],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.notification_sent', true);

    Mail::assertSent(\App\Mail\ReturnRequestMail::class, function ($mail) use ($order) {
        return $mail->hasTo($order->user->email)
            && $mail->order->is($order)
            && $mail->returnRequest->reason === 'Wrong size';
    });
});

it('does not send a return request email when the order belongs to another user', function () {
    Mail::fake();

    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    Sanctum::actingAs($intruder);

    $order = Order::create([
        'user_id' => $owner->id,
        'status' => 'delivered',
        'total' => 500.00,
    ]);

    $item = new OrderItem([
        'order_id' => $order->id,
        'product_name' => 'Silk Dupatta',
        'sku' => 'SD-002',
        'unit_price' => 500.00,
        'quantity' => 1,
        'line_total' => 500.00,
    ]);
    $item->save();

    // Scoped to the token's owner, a foreign order reads as 404 (not 403) so
    // the API never confirms which order ids exist.
    $response = postJson(route('api.orders.return-requests.store', $order), [
        'reason' => 'Damaged',
        'payout_method' => 'bank',
        'account_holder' => 'R Sharma',
        'account_number' => '1234567890',
        'ifsc' => 'SBIN0123456',
        'items' => [['order_item_id' => $item->id, 'quantity' => 1]],
    ]);

    $response->assertNotFound();
    Mail::assertNotSent(\App\Mail\ReturnRequestMail::class);
});

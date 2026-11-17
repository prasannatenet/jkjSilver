<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

test('customer profile includes their details and complete paginated order history', function () {
    $user = User::factory()->create([
        'name' => 'Customer Example',
        'email' => 'customer@example.com',
        'phone' => '9876543210',
    ]);
    $otherUser = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'status' => 'processing',
        'total' => 1250,
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_name' => 'Silver Necklace',
        'unit_price' => 1250,
        'quantity' => 1,
        'line_total' => 1250,
    ]);
    Order::create([
        'user_id' => $otherUser->id,
        'status' => 'delivered',
        'total' => 500,
    ]);

    $this->actingAs($user)
        ->get(route('account.profile'))
        ->assertOk()
        ->assertSee('Customer Example')
        ->assertSee('customer@example.com')
        ->assertSee('9876543210')
        ->assertSee('Order #'.$order->id)
        ->assertSee('Silver Necklace')
        ->assertDontSee('Order #'.Order::where('user_id', $otherUser->id)->first()->id);
});

test('customer account pages require authentication', function () {
    $this->get(route('account.profile'))
        ->assertRedirect(route('login'));
});

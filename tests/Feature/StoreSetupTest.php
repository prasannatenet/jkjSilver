<?php

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Spatie\Permission\Models\Role;

function storeSetupAdmin(): User
{
    Role::firstOrCreate(['name' => 'admin']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    return $admin;
}

test('store setup is available only to administrators', function () {
    $this->get(route('admin.store-setup.edit'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('admin.store-setup.edit'))
        ->assertRedirect();

    $this->actingAs(storeSetupAdmin())
        ->get(route('admin.store-setup.edit'))
        ->assertOk()
        ->assertSee('Store Setup')
        ->assertSee('Shipping Policy')
        ->assertSee('Return Policy')
        ->assertSee('Minimum order amount for free shipping');
});

test('admin saves policy points and the free shipping minimum displayed on product details', function () {
    $admin = storeSetupAdmin();

    $this->actingAs($admin)
        ->put(route('admin.store-setup.update'), [
            'shipping_policy' => ['Ships in 2 business days', 'Tracking details are emailed'],
            'return_policy' => ['Returns accepted within 14 days'],
            'free_shipping_minimum' => '2500',
        ])
        ->assertRedirect(route('admin.store-setup.edit'))
        ->assertSessionHas('success');

    $setting = Setting::query()->firstOrFail();
    expect($setting->shipping_policy)->toBe(['Ships in 2 business days', 'Tracking details are emailed'])
        ->and($setting->return_policy)->toBe(['Returns accepted within 14 days'])
        ->and((float) $setting->free_shipping_minimum)->toBe(2500.0)
        ->and(Setting::shippingChargeFor(2499.99))->toBe(199.0)
        ->and(Setting::shippingChargeFor(2500.0))->toBe(0.0);

    $product = Product::create([
        'name' => 'Store Setup Test Ring',
        'slug' => 'store-setup-test-ring',
        'base_price' => 2000,
        'product_type' => 'simple',
        'is_active' => true,
    ]);

    $this->get(route('product.show', $product->slug))
        ->assertOk()
        ->assertSee('Ships in 2 business days')
        ->assertSee('Tracking details are emailed')
        ->assertSee('Returns accepted within 14 days')
        ->assertSee('₹2,500.00');
});

test('store setup enforces 500 characters across all points in each policy', function () {
    $this->actingAs(storeSetupAdmin())
        ->put(route('admin.store-setup.update'), [
            'shipping_policy' => [str_repeat('s', 250), str_repeat('h', 251)],
            'return_policy' => [str_repeat('r', 500)],
            'free_shipping_minimum' => '5000',
        ])
        ->assertSessionHasErrors('shipping_policy');
});

<div class="cart-drawer-content">
    @forelse ($headerCartItems ?? collect() as $item)
        <div class="cart-drawer-item">
            <img src="{{ $item['image'] }}" alt="" loading="lazy">
            <span class="wishlist-drawer-item-details">
                @if ($item['slug'])
                    <a class="wishlist-drawer-item-name" href="{{ url('/product/' . $item['slug']) }}">{{ $item['name'] }}</a>
                @else
                    <span class="wishlist-drawer-item-name">{{ $item['name'] }}</span>
                @endif
                <span class="cart-drawer-item-quantity">Qty: {{ $item['quantity'] }}</span>
                <span class="wishlist-drawer-item-price">₹ {{ number_format((float) $item['subtotal'], 2) }}</span>
            </span>
            <button
                class="cart-drawer-item-remove"
                type="button"
                aria-label="Remove {{ $item['name'] }} from shopping bag"
                data-cart-remove-url="{{ route('storefront.cart.remove') }}"
                @if ($item['cart_id'])
                    data-cart-id="{{ $item['cart_id'] }}"
                @else
                    data-product-id="{{ $item['product_id'] }}"
                    data-product-variation-id="{{ $item['product_variation_id'] ?? '' }}"
                @endif
            >Remove</button>
        </div>
    @empty
        <p class="wishlist-drawer-empty">Your shopping bag is empty.</p>
    @endforelse
</div>
@if (($headerCartItems ?? collect())->isNotEmpty())
    <div class="cart-drawer-footer">
        <div class="cart-drawer-subtotal">
            <span>Subtotal</span>
            <strong>₹ {{ number_format((float) ($headerCartSubtotal ?? 0), 2) }}</strong>
        </div>
    </div>
@endif

@extends('layouts.frontend')

@section('title', 'My Account')

@section('content')
    <main class="account-page">
        <div class="account-container">
            <header class="account-page-header">
                <p class="account-eyebrow">MY ACCOUNT</p>
                <h1>Welcome, {{ $user->name }}</h1>
                <p>Manage your profile and keep track of your orders.</p>
            </header>

            <section class="account-profile" aria-labelledby="account-profile-title">
                <div>
                    <p class="account-eyebrow">PERSONAL DETAILS</p>
                    <h2 id="account-profile-title">Your Profile</h2>
                </div>
                <dl>
                    <div>
                        <dt>Name</dt>
                        <dd>{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $user->phone ?: 'Not provided' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="account-orders" id="order-history" aria-labelledby="order-history-title">
                <div class="account-orders-header">
                    <div>
                        <p class="account-eyebrow">YOUR PURCHASES</p>
                        <h2 id="order-history-title">Order History</h2>
                    </div>
                    <span>{{ $orders->total() }} {{ \Illuminate\Support\Str::plural('order', $orders->total()) }}</span>
                </div>

                @forelse ($orders as $order)
                    <article class="account-order">
                        <header class="account-order-header">
                            <div>
                                <h3>Order #{{ $order->id }}</h3>
                                <p>Placed {{ $order->created_at->format('M j, Y') }}</p>
                            </div>
                            <span class="account-order-status">{{ ucfirst($order->status) }}</span>
                        </header>

                        <ul class="account-order-items">
                            @foreach ($order->items as $item)
                                <li>
                                    <span>{{ $item->product_name }} <span class="account-item-quantity">× {{ $item->quantity }}</span></span>
                                    <span>₹{{ number_format((float) $item->line_total, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <footer class="account-order-total">
                            <span>Total</span>
                            <strong>₹{{ number_format((float) $order->total, 2) }}</strong>
                        </footer>
                    </article>
                @empty
                    <div class="account-orders-empty">
                        <h3>No orders yet</h3>
                        <p>Your orders will appear here after you make a purchase.</p>
                        <a href="{{ route('storefront.products') }}">Explore jewellery</a>
                    </div>
                @endforelse

                @if ($orders->hasPages())
                    <nav class="account-pagination" aria-label="Order history pages">
                        {{ $orders->links() }}
                    </nav>
                @endif
            </section>
        </div>
    </main>
@endsection

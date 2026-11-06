@extends('layouts.app')
@section('content')
    <div class="df-page-header">
        <h1 class="df-page-title">Store Setup</h1>
        <nav class="df-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="separator"><i class="bi bi-chevron-right"></i></span>
            <span class="current">Store Setup</span>
        </nav>
    </div>

    @if (session('success'))
        <div class="df-alert df-alert-success">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="df-alert df-alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.store-setup.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="df-card h-100">
                    <div class="df-card-header">
                        <h5 class="df-card-title"><i class="bi bi-truck"></i> Shipping Policy</h5>
                    </div>
                    <div class="df-card-body">
                        <p class="text-muted">Add policy points one at a time. The total policy is limited to 500 characters.</p>
                        <div class="policy-points" data-policy-points="shipping_policy" data-limit="500">
                            @php
                                $shippingPoints = old('shipping_policy', $setting?->shipping_policy ?? []);
                                $shippingPoints = is_array($shippingPoints) && count($shippingPoints) ? $shippingPoints : [''];
                            @endphp
                            @foreach ($shippingPoints as $point)
                                <div class="input-group mb-2 policy-point">
                                    <input type="text" name="shipping_policy[]" class="df-form-control"
                                           value="{{ $point }}" maxlength="500" placeholder="Enter one shipping policy point">
                                    <button type="button" class="btn btn-outline-danger remove-policy-point" aria-label="Remove point">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="df-btn df-btn-secondary add-policy-point">
                                <i class="bi bi-plus-circle"></i> Add Point
                            </button>
                            <small class="text-muted policy-counter" aria-live="polite">0 / 500 characters</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="df-card h-100">
                    <div class="df-card-header">
                        <h5 class="df-card-title"><i class="bi bi-arrow-counterclockwise"></i> Return Policy</h5>
                    </div>
                    <div class="df-card-body">
                        <p class="text-muted">Add policy points one at a time. The total policy is limited to 500 characters.</p>
                        <div class="policy-points" data-policy-points="return_policy" data-limit="500">
                            @php
                                $returnPoints = old('return_policy', $setting?->return_policy ?? []);
                                $returnPoints = is_array($returnPoints) && count($returnPoints) ? $returnPoints : [''];
                            @endphp
                            @foreach ($returnPoints as $point)
                                <div class="input-group mb-2 policy-point">
                                    <input type="text" name="return_policy[]" class="df-form-control"
                                           value="{{ $point }}" maxlength="500" placeholder="Enter one return policy point">
                                    <button type="button" class="btn btn-outline-danger remove-policy-point" aria-label="Remove point">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="df-btn df-btn-secondary add-policy-point">
                                <i class="bi bi-plus-circle"></i> Add Point
                            </button>
                            <small class="text-muted policy-counter" aria-live="polite">0 / 500 characters</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="df-card">
                    <div class="df-card-header">
                        <h5 class="df-card-title"><i class="bi bi-currency-rupee"></i> Free Shipping</h5>
                    </div>
                    <div class="df-card-body">
                        <div class="col-md-6">
                            <label for="free_shipping_minimum" class="df-form-label">Minimum order amount for free shipping</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input id="free_shipping_minimum" type="number" name="free_shipping_minimum"
                                       class="df-form-control" min="0" step="0.01" required
                                       value="{{ old('free_shipping_minimum', $setting?->free_shipping_minimum ?? 5000) }}">
                            </div>
                            <small class="text-muted">Orders at or above this subtotal will receive free shipping.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="df-btn df-btn-primary">
                    <i class="bi bi-check2-circle"></i> Save Store Setup
                </button>
            </div>
        </div>
    </form>

    <template id="policy-point-template">
        <div class="input-group mb-2 policy-point">
            <input type="text" class="df-form-control" maxlength="500" placeholder="Enter one policy point">
            <button type="button" class="btn btn-outline-danger remove-policy-point" aria-label="Remove point">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </template>

    <script>
        document.querySelectorAll('[data-policy-points]').forEach(function (list) {
            const field = list.dataset.policyPoints;
            const counter = list.parentElement.querySelector('.policy-counter');
            const limit = Number(list.dataset.limit);

            function updateCount() {
                const count = Array.from(list.querySelectorAll('input')).reduce(function (total, input) {
                    return total + Array.from(input.value).length;
                }, 0);
                counter.textContent = count + ' / ' + limit + ' characters';
                counter.classList.toggle('text-danger', count > limit);
            }

            list.addEventListener('input', updateCount);
            list.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.remove-policy-point');
                if (!removeButton) return;
                removeButton.closest('.policy-point').remove();
                updateCount();
            });

            list.parentElement.querySelector('.add-policy-point').addEventListener('click', function () {
                const point = document.querySelector('#policy-point-template').content.cloneNode(true);
                const input = point.querySelector('input');
                input.name = field + '[]';
                input.placeholder = field === 'shipping_policy'
                    ? 'Enter one shipping policy point'
                    : 'Enter one return policy point';
                list.appendChild(point);
                list.querySelector('.policy-point:last-child input').focus();
                updateCount();
            });

            updateCount();
        });
    </script>
@endsection

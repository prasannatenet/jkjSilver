@php
    // Live gold & silver rates, served from MetalPriceService via the
    // "layouts.topbar" view composer. The default keeps the bar rendering even
    // if the composer is ever bypassed.
    $liveRates = $liveRates ?? ['available' => false, 'metals' => [], 'flat' => [], 'message' => null, 'stale' => false, 'unit_label' => ''];
@endphp

<div class="top-bar" id="topBar">
    <div class="top-bar-inner">

        {{-- ===== LIVE METAL RATES ===== --}}
        <div class="live-rates" id="liveRates" data-live-rates-url="{{ route('metal-prices.index') }}">
            <button type="button" class="live-rates-btn{{ $liveRates['available'] ? '' : ' is-offline' }}"
                id="liveRatesBtn" aria-expanded="false" aria-controls="liveRatesPanel"
                aria-label="Live gold and silver rates">
                <span class="live-pulse" aria-hidden="true"></span>
                <span class="live-label">LIVE</span>
                <i class="bi bi-chevron-down live-caret" aria-hidden="true"></i>
            </button>

            <div class="live-rates-panel" id="liveRatesPanel" role="dialog" aria-modal="false"
                aria-label="Live gold and silver rates" aria-hidden="true">
                <div class="live-panel-head">
                    <div>
                        <span class="live-panel-kicker">Spot Rates</span>
                        <h2 class="live-panel-title">Gold &amp; Silver</h2>
                    </div>
                    <button type="button" class="live-panel-close" id="liveRatesClose" aria-label="Close live rates">
                        &times;
                    </button>
                </div>

                <div class="live-panel-body">
                    @forelse($liveRates['metals'] as $metal)
                        <article class="live-metal" data-live-metal="{{ $metal['key'] }}"
                            data-live-direction="{{ $metal['direction'] }}">
                            <div class="live-metal-top">
                                <span class="live-metal-name">
                                    <i class="bi bi-{{ $metal['key'] === 'gold' ? 'gem' : 'circle-half' }}" aria-hidden="true"></i>
                                    {{ $metal['name'] }}
                                </span>
                                <span class="live-metal-change is-{{ $metal['direction'] }}"
                                    data-live-dir="{{ $metal['key'] }}.direction">
                                    @if($metal['direction'] === 'up')
                                        <i class="bi bi-caret-up-fill" aria-hidden="true"></i>
                                    @elseif($metal['direction'] === 'down')
                                        <i class="bi bi-caret-down-fill" aria-hidden="true"></i>
                                    @else
                                        <i class="bi bi-dash" aria-hidden="true"></i>
                                    @endif
                                    {{ $metal['change_percent'] }}
                                </span>
                            </div>

                            <div class="live-metal-price" data-live="{{ $metal['key'] }}.price">{{ $metal['price'] }}</div>
                            <div class="live-metal-unit">{{ $liveRates['unit_label'] }}</div>

                            <dl class="live-metal-stats">
                                <div>
                                    <dt>Day Change</dt>
                                    <dd data-live="{{ $metal['key'] }}.change">{{ $metal['change'] }}</dd>
                                </div>
                                <div>
                                    <dt>High</dt>
                                    <dd data-live="{{ $metal['key'] }}.high">{{ $metal['high'] }}</dd>
                                </div>
                                <div>
                                    <dt>Low</dt>
                                    <dd data-live="{{ $metal['key'] }}.low">{{ $metal['low'] }}</dd>
                                </div>
                            </dl>

                            @if($metal['purity'])
                                <div class="live-metal-purity">
                                    @foreach($metal['purity'] as $purityKey => $purity)
                                        <span class="live-purity-chip">
                                            <em>{{ $purity['label'] }}</em>
                                            <span data-live="{{ $metal['key'] }}.purity.{{ $purityKey }}">{{ $purity['display'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <p class="live-panel-empty">{{ $liveRates['message'] ?? 'Live rates are temporarily unavailable.' }}</p>
                    @endforelse
                </div>

                <div class="live-panel-foot">
                    <span class="live-panel-updated">
                        <i class="bi bi-clock-history" aria-hidden="true"></i>
                        Updated <span data-live="updated_at">{{ $liveRates['updated_at'] ?? '—' }}</span>
                    </span>
                    <button type="button" class="live-panel-refresh" id="liveRatesRefresh" aria-label="Refresh live rates">
                        <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <span class="top-bar-promo">FLAT 15% Off on select Silver Jewellery</span>
    </div>

    {{-- Rates are indicative spot prices, refreshed periodically. --}}
    <p class="visually-hidden">Gold and silver spot prices are indicative and may differ from the final billed price.</p>

<link rel="stylesheet" href="{{ asset('frontend/css/topbar.css') }}">

</div>

<?php

it('renders the storefront footer with its app and payment assets', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('About JK Silver Store')
        ->assertSee('aria-label="Quick links"', false)
        ->assertSee('aria-label="Services"', false)
        ->assertSee('aria-label="Your account"', false)
        ->assertSee(asset('frontend/images/apple-logo.png'), false)
        ->assertSee(asset('frontend/images/playstore.png'), false)
        ->assertSee(asset('frontend/images/payment.png'), false);
});

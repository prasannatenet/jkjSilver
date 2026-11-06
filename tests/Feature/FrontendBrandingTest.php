<?php

use App\Models\Setting;

it('renders frontend branding from saved settings', function () {
    $setting = new Setting([
        'site_name' => 'Configured Store',
        'logo_path' => 'settings/store-logo.png',
        'favicon_path' => 'settings/store-favicon.ico',
    ]);

    $html = view('layouts.frontend')
        ->with('appSetting', $setting)
        ->render();

    expect($html)
        ->toContain('<title>Configured Store</title>')
        ->toContain('href="'.asset('storage/settings/store-favicon.ico').'"')
        ->toContain('src="'.asset('storage/settings/store-logo.png').'"')
        ->toContain('alt="Configured Store"');
});

it('uses default frontend branding when no settings are configured', function () {
    $html = view('layouts.frontend')
        ->with('appSetting', null)
        ->render();

    expect($html)
        ->toContain('<title>Maanyas</title>')
        ->toContain('href="'.asset('favicon.ico').'"')
        ->toContain('src="'.asset('frontend/images/logo-jk-320.jpeg').'"')
        ->toContain('alt="Maanyas"');
});

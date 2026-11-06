<?php

use App\Models\Slider;

it('renders active homepage sliders in their configured order', function () {
    Slider::create([
        'title' => 'Second slide',
        'image_path' => 'sliders/second.jpg',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    Slider::create([
        'title' => 'First slide',
        'image_path' => 'sliders/first.jpg',
        'sort_order' => 1,
        'is_active' => true,
        'button_link' => '/collections',
    ]);

    Slider::create([
        'title' => 'Inactive slide',
        'image_path' => 'sliders/inactive.jpg',
        'sort_order' => 0,
        'is_active' => false,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSeeInOrder([
            'storage/sliders/first.jpg',
            'alt="First slide"',
            'storage/sliders/second.jpg',
            'alt="Second slide"',
        ])
        ->assertSee('href="/collections"', false)
        ->assertDontSee('Inactive slide');
});

it('keeps the default banners when no active slider has an image', function () {
    Slider::create([
        'title' => 'No image',
        'sort_order' => 0,
        'is_active' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('frontend/images/slide_01.jpg')
        ->assertSee('frontend/images/slide_02.jpg');
});

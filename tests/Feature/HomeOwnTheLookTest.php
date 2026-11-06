<?php

it('loads the Own the Look image assets and relevant card labels', function () {
    $html = $this->get('/')
        ->assertOk()
        ->getContent();

    $sectionStart = strpos($html, '<!-- Own The Look -->');
    $sectionEnd = strpos($html, '</section>', $sectionStart) + strlen('</section>');
    $ownTheLookSection = substr($html, $sectionStart, $sectionEnd - $sectionStart);

    expect($ownTheLookSection)
        ->toContain(asset('frontend/images/hotspot-image.jpg'))
        ->toContain(asset('frontend/images/silver-1.webp'))
        ->toContain(asset('frontend/images/silver-4.webp'))
        ->toContain(asset('frontend/images/toering_01.webp'))
        ->toContain(asset('frontend/images/toering_06.webp'))
        ->toContain('Silver Anklet')
        ->toContain('Silver Toe Ring')
        ->not->toContain('assets/images/');
});

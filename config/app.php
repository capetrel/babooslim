<?php

return [
    'env' => isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] ?: 'production',
    'debug' => isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true' ? true : false,
    'local' => 'fr_FR',
    'site' => [
        'name' => 'example',
        'url' => isset($_ENV['APP_DOMAIN']) && $_ENV['APP_DOMAIN'] ? 'http://' . $_ENV['APP_DOMAIN'] : 'https://www.example.test',
        'slogan' => 'Example',
        'description' => 'Example',
        'social_img_name' => 'social-share',
        'designer' => 'Designer',
    ],
    'company' => [
        'name' => "Example Corp.",
        'address' => '123 rue Azerty-Jean - 01234 Ville',
    ]
];

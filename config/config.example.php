<?php
// Copy to config.php. Never commit config.php or use local development settings online.
return [
    'base_url' => 'http://localhost:8080/uc-properties',
    'timezone' => 'Africa/Lagos',
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'uc_properties',
        'user' => 'root',
        'password' => '',
    ],
    // Production: HTTPS, a dedicated database user, and a long random secret are required.
    'app_secret' => 'REPLACE_WITH_AT_LEAST_32_RANDOM_CHARACTERS',
    'mail' => [
        'enabled' => false,
        'to' => 'info@ucpropertiesltd.com',
        'from' => 'website@ucpropertiesltd.com',
    ],
];

<?php

$env = __DIR__ . '/../.env';
$data = [];
if (file_exists($env)) {
    $data = M1\Env\Parser::parse(file_get_contents($env));
}
define('ENV_DATA', $data);

foreach (['YII_DEBUG', 'YII_ENV', 'ADMIN_PASS'] as $key) {
    if (!defined($key) && (isset($data[$key]) || isset($_ENV[$key]))) {
        define($key, $data[$key] ?? $_ENV[$key]);
    }
}

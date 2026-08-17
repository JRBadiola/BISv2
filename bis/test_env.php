<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/system/Test/bootstrap.php';

echo "Testing CodeIgniter environment...\n\n";

$key = env('OPENROUTER_API_KEY', '');

if (empty($key)) {
    echo "ERROR: OPENROUTER_API_KEY is EMPTY or NOT LOADED.\n";
} else {
    echo "SUCCESS: OPENROUTER_API_KEY is loaded.\n";
    echo "Key length: " . strlen($key) . "\n";
    echo "Key prefix: " . substr($key, 0, 12) . "...\n";
}
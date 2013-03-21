<?php

if (!is_file(__DIR__ . '/../vendor/autoload.php')) {
    throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
}

require_once __DIR__ . '/../vendor/autoload.php';

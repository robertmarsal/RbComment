<?php
$composerAutoloadFile = __DIR__ . '/../vendor/autoload.php';

if (file_exists($composerAutoloadFile)) {
    $loader = require_once $composerAutoloadFile;
} else {
    throw new RuntimeException('vendor/autoload.php is missing. Please execute `composer install`');
}

unset($composerAutoloadFile, $loader);

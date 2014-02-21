<?php

function includeIfExists($file)
{

    if (file_exists($file)) {
        return include $file;
    }
}

if (!extension_loaded('curl') || !function_exists('curl_init')) {
    die(<<<EOT
cURL has to be enabled!
EOT
    );
}

if ((!$loader = includeIfExists(__DIR__ . '/../vendor/autoload.php'))) {
    die(<<<EOT
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT
    );
}

define('USE_MOCK', true);

define('QUEUE_ACCOUNT', getenv('QUEUE_ACCOUNT'));
define('QUEUE_USERNAME', getenv('QUEUE_USERNAME'));
define('QUEUE_API_KEY', getenv('QUEUE_API_KEY'));

$loader->add('Tests', __DIR__);
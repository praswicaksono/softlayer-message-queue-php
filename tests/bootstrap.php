<?php

set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'src');

require_once 'SoftLayer/Messaging.php';

if(!function_exists('validate_json'))
{
    function validate_json($json) {
        if(json_decode($json) === null) {
            die("Invalid JSON provided in test suite.");
        }
    }
}

define('USE_MOCK', false);

define('QUEUE_ACCOUNT', getenv('QUEUE_ACCOUNT'));
define('QUEUE_USERNAME', getenv('QUEUE_USERNAME'));
define('QUEUE_API_KEY', getenv('QUEUE_API_KEY'));

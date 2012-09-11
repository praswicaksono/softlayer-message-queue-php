<?php

set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'src');

define('QUEUE_ACCOUNT',  getenv('QUEUE_ACCOUNT'));  // Your queue account id.
define('QUEUE_USERNAME', getenv('QUEUE_USERNAME')); // Your portal username.
define('QUEUE_API_KEY',  getenv('QUEUE_API_KEY'));  // Your portal API key.

require_once 'SoftLayer/Messaging.php';

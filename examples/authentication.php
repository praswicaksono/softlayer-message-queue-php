<?php

include 'bootstrap.php';

use SoftLayer\SoftLayer_Messaging;

$messaging = new SoftLayer_Messaging();

if($messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY)) {
    echo "Welcome to the SoftLayer Message Queue!" . PHP_EOL;
}

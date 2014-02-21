<?php

use SoftLayer\SoftLayer_Messaging;

include 'bootstrap.php';

$messaging = new SoftLayer_Messaging();

if ($messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY)) {
    echo "Welcome to the SoftLayer Message Queue!" . PHP_EOL;
}

<?php

include 'bootstrap.php';


$messaging = new SoftLayer_Messaging();
$messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);


// I first need a queue to push my messages to.
$queue = $messaging->queue('myQueue')->create();


// Now I can start creating messages.
for($i = 0; $i < 10; $i++) {
    $queue->message("Example Message {$i}")->create();
}


// Should have all 10 available after a tick.
sleep(1);


// By default messages() fetches a single message. It has an optional
// $batch parameter which allows me to fetch up to 100.
$messages = $queue->messages(10);

foreach($messages as $m) {
    echo $m->getId() . ' : ' . $m->getBody() . PHP_EOL;
}


// Each of the message objects can delete itself once I complete
// my work.
foreach($messages as $m) {
    echo "Deleting: " . $m->getId() . PHP_EOL;
    $m->delete();
}


// If I need to, I can delete a message by its ID (say, if I lose the
// object reference to it but still have the ID in a database somewhere).
$id = $messages[0]->getId();

// Of course this will throw a 404 exception as I've already deleted it.
try {
    $messaging->queue('myQueue')->message()->delete($id);
} catch(Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}


// I can't delete a queue with messages in it by default, but with the 
// $force parameter set to "true", I can.
$queue->delete(true);

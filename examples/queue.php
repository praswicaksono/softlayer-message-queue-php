<?php

use SoftLayer\SoftLayer_Messaging;

include 'bootstrap.php';

$messaging = new SoftLayer_Messaging();
$messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);


// I can create a queue very simply by providing a name.
$messaging->queue('myFirstQueue')->create();


// Or, I can set various attributes and then create it.
$messaging->queue('mySecondQueue')
    ->setVisibilityInterval(20)
    ->addTag('tag1')
    ->addTag('tag2')
    ->create();


// The queue() method returns a valid queue object with all
// connectivity and auth persisted, so I can do this as well:
$queue = $messaging->queue();


// Then use that object, even calling create() on it directly.
$queue->setName('myThirdQueue');
$queue->addTag('tag1');
$queue->create();


// I can also use a generic save() if I'm uncertain if the
// queue already exists.
$queue->save();


// Now that I have a few queues created, I can get a list of
// them.
$queues = $messaging->queues();


foreach ($queues as $q) {
    // Each of these $q objects are also fully functional,
    // so I can add another tag and save it here.
    $q->addTag('tag3');
    $q->update();

    echo $q->getName() . '(' . implode(',', $q->getTags()) . ')' . PHP_EOL;
}


// Finally, I can remove them by name.
$messaging->queue('myFirstQueue')->delete();
$messaging->queue('mySecondQueue')->delete();


// I still have access to $queue for 'myThirdQueue', so I can 
// delete it using the object directly.
$queue->delete();

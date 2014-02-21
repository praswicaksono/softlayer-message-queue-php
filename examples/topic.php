<?php

use SoftLayer\Messaging\SoftLayer_Messaging_Endpoint;
use SoftLayer\SoftLayer_Messaging;

include 'bootstrap.php';


$messaging = new SoftLayer_Messaging();
$messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);

// Much like creating a queue, all I need to provide for a topic is a name.
$topic = $messaging->topic('myFirstTopic')->create();


// To actually do something, topics need subscribers. I can add either
// HTTP and/or queue subscriptions.


// A queue must exist before creating a subscription which targets it.
$queue1 = $messaging->queue('myTargetQueue1')->create();
$queue2 = $messaging->queue('myTargetQueue2')->create();


// First I'll create an endpoint targeting each queue.
$queueEndpoint1 = SoftLayer_Messaging_Endpoint::endpointByType('queue');
$queueEndpoint1->setQueueName($queue1->getName());

$queueEndpoint2 = SoftLayer_Messaging_Endpoint::endpointByType('queue');
$queueEndpoint2->setQueueName($queue2->getName());


// Then I add them to the topic as individual subscriptions.
$topic->subscription()
    ->setEndpointType('queue')
    ->setEndpoint($queueEndpoint1)
    ->create();

$topic->subscription()
    ->setEndpointType('queue')
    ->setEndpoint($queueEndpoint2)
    ->create();


// Now the topic should tell me I have 2 subscribers, each targeting a
// different queue.
$subscriptions = $topic->subscriptions();

foreach ($subscriptions as $s) {
    echo $s->getId() . ' : ' . $s->getEndpointType() . ' : ' . $s->getEndpoint()->getQueueName() . PHP_EOL;
}


// Finally, I can push a message to the topic. That message will subsequently
// end up in both target queues.
$topic->message('Example Message 1')->create();


// The message needs a moment to be dispatched and visible.
sleep(10);


if (count($queue1->messages()) > 0) {
    echo "Queue 1 got the message!" . PHP_EOL;
}
if (count($queue2->messages()) > 0) {
    echo "Queue 2 got the message!" . PHP_EOL;
}


// Finally, I can delete the topic and all its subscribers.
$topic->delete(true);


// Might as well remove the target queues as well!
$queue1->delete(true);
$queue2->delete(true);

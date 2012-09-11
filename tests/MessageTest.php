<?php

require_once 'bootstrap.php';
require_once 'mock.php';

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateMessage()
    {
        $messaging = new SoftLayer_Messaging();

        $queueName = 'testQueue01';

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
        }

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);
        $messaging->queue($queueName)->create();

        $messaging->queue($queueName)->message()->setBody('Example body')->create();

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals("POST", $request->getMethod());
        $this->assertEquals("/queues/{$queueName}/messages", $request->getPath());

        $this->assertEquals(201, $response->getStatus());

        $messaging->queue($queueName)->delete(true);
    }

    public function testPopMessages()
    {
        $messaging = new SoftLayer_Messaging();

        $queueName = 'testQueuePopMessages01';

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::messages());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
        }

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);
        $messaging->queue($queueName)->create();

        $messaging->queue($queueName)->message()->setBody('Example 1')->create();
        $messaging->queue($queueName)->message()->setBody('Example 2')->create();
        $messaging->queue($queueName)->message()->setBody('Example 3')->create();

        if(USE_MOCK == false) {
            sleep(10);
        }

        $messages = $messaging->queue($queueName)->messages(3);

        $this->assertEquals(3, count($messages));

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals("GET", $request->getMethod());
        $this->assertEquals("/queues/{$queueName}/messages", $request->getPath());

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(3, $response->getBody()->item_count);
        $this->assertEquals(3, count($response->getBody()->items));

        $messaging->queue($queueName)->delete(true);
    }
}


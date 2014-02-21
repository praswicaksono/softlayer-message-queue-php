<?php

namespace Test\SoftLayer;

use SoftLayer\SoftLayer_Messaging;
use SoftLayer\Http\Adapter\SoftLayer_Http_Adapter_Mock;
use Tests\SoftLayer\Mock;

class MessageTest extends \PHPUnit_Framework_TestCase
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

        if(!USE_MOCK) {
            try {
                $messaging->queue($queueName)->delete(true);
            } catch(Exception $e) {
                // ...
            }
        }

        sleep(2);

        $messaging->queue($queueName)->create();

        sleep(2);

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

        if(!USE_MOCK) {
            try {
                $messaging->queue($queueName)->delete(true);
            } catch(Exception $e) {
                // ...
            }
        }

        sleep(2);

        $messaging->queue($queueName)->create();

        sleep(2);

        $messaging->queue($queueName)->message()->setBody('Example 1')->create();
        $messaging->queue($queueName)->message()->setBody('Example 2')->create();
        $messaging->queue($queueName)->message()->setBody('Example 3')->create();

        if(USE_MOCK == false) {
            sleep(10);

            $this->assertEquals(3, $messaging->queue($queueName)->getMessageCount());
            $this->assertEquals(3, $messaging->queue($queueName)->getVisibleMessageCount());
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

    public function testPushALotOfMessages()
    {
        // Only a functional test for now.
        if(USE_MOCK) {
            return;
        }

        $queueName = "pushALotOfMessages01";

        $messaging = new SoftLayer_Messaging();
        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);

        try {
            $messaging->queue($queueName)->delete(true);
        } catch(Exception $e) {
            // ...
        }

        $messaging->queue($queueName)->create();

        $failed = 0;

        for($i = 0; $i < 1000; $i++) {
            $messaging->queue($queueName)->message()->setBody("Example {$i}")->create();

            if($messaging->getClient()->getResponse()->getStatus() >= 400) {
                $failed += 1;
            }
        }

        $this->assertEquals(0, $failed);
    }
}


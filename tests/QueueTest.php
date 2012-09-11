<?php

require_once 'bootstrap.php';
require_once 'mock.php';

class QueueTest extends PHPUnit_Framework_TestCase
{
    public function testQueuesList()
    {
        $messaging = new SoftLayer_Messaging();

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::queues());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
        }

        $queueName = 'testQueueList01';

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);
        $messaging->queue($queueName)->create();

        $queues = $messaging->queues();

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/queues', $request->getPath());
        $this->assertNotEmpty($request->getHeader('X-Auth-Token'));

        $this->assertEquals(true, is_array($queues));
        $this->assertEquals('SoftLayer_Messaging_Queue', get_class(array_shift($queues)));

        // It's fine if this is empty, but the document should always have
        // basic structure.
        $this->assertGreaterThanOrEqual(0, $response->getBody()->item_count);
        $this->assertCount($response->getBody()->item_count, $response->getBody()->items);

        $messaging->queue($queueName)->delete();
    }

    public function testQueueSerialization()
    {
        $queue = new SoftLayer_Messaging_Queue();
        $queue->setName("queue");
        $queue->addTag("tag1");
        $queue->addTag("tag2");
        $queue->setVisibilityInterval(123456789);
        $queue->setExpiration(987654321);

        // Supressed by honoring static::$emit
        $queue->doNotEmit = 123;

        $this->assertEquals(json_decode(Mock::serializedQueue()), $queue->serialize());
    }

    public function testQueueCreationAndFetching()
    {
        $messaging = new SoftLayer_Messaging();

        $queueName = 'testQueueDetail01';

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::queueDetail());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
        }

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);
        $messaging->queue($queueName)
            ->setVisibilityInterval(30)
            ->setExpiration(604800)
            ->addTag('tag1')
            ->addTag('tag2')
            ->create();

        $this->assertEquals(201, $messaging->getClient()->getResponse()->getStatus());
        $this->assertEquals($queueName, $messaging->queue($queueName)->fetch()->getName());

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $body = $response->getBody();

        $this->assertEquals($queueName, $body->name);
        $this->assertEquals(30, $body->visibility_interval);
        $this->assertEquals(604800, $body->expiration);
        $this->assertEquals(array('tag1', 'tag2'), $body->tags);

        $messaging->queue($queueName)->delete();
    }

    public function testQueueCreationAndDeletion()
    {
        $messaging = new SoftLayer_Messaging();

        $queueName = 'testQueueDeletion01';

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
        }

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);

        // Test creation
        $messaging->queue()
            ->setName($queueName)
            ->create();

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('Object created', $response->getBody()->message);

        // ...and deletion
        $messaging->queue($queueName)->delete();

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('Object queued for deletion', $response->getBody()->message);
    }

    public function testQueueUpdate()
    {
        // Only a functional test for now.
        if(USE_MOCK) {
            return;
        }

        $messaging = new SoftLayer_Messaging();
        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);

        $queueName = 'testQueueUpdate01';

        $messaging->queue()
            ->setName($queueName)
            ->setVisibilityInterval(100)
            ->create();

        // May be just updating it if the queue already exists.
        $this->assertContains($messaging->getClient()->getResponse()->getStatus(), array(200, 201));

        $queue = $messaging->queue($queueName)->fetch();

        // Are we getting back what we gave it?
        $this->assertEquals($queueName, $queue->getName());
        $this->assertEquals(100, $queue->getVisibilityInterval());
    }
}

<?php

require_once 'bootstrap.php';
require_once 'mock.php';

class TopicTest extends PHPUnit_Framework_TestCase
{
    public function testTopicsList()
    {
        $messaging = new SoftLayer_Messaging();

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::topics());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
        }

        $topicName = 'testTopic01';

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);
        $messaging->topic($topicName)->create();

        $topics = $messaging->topics();

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();
        
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/topics', $request->getPath());
        $this->assertNotEmpty($request->getHeader('X-Auth-Token'));

        $this->assertEquals(true, is_array($topics));
        $this->assertEquals('SoftLayer_Messaging_Topic', get_class(array_shift($topics)));

        // It's fine if this is empty, but the document should always have
        // basic structure.
        $this->assertGreaterThanOrEqual(0, $response->getBody()->item_count);
        $this->assertCount($response->getBody()->item_count, $response->getBody()->items);

        $messaging->topic($topicName)->delete();
    }

    public function testTopicSerialization()
    {
        $topic = new SoftLayer_Messaging_Topic();
        $topic->setName('topic');
        $topic->addTag('tag1');
        $topic->addTag('tag2');

        $this->assertEquals(json_decode(Mock::serializedTopic()), $topic->serialize());
    }

    public function testCreateQueueAndHttpEndpointSubscriptions()
    {
        $messaging = new SoftLayer_Messaging();

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectCreated());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::subscriptions());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::objectDeleted());
        }

        $topicName = 'testSubscriptionCreation01';
        $queueName = 'testQueueForSubscription';

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);
        $messaging->topic($topicName)->create();

        // Create an HTTP endpoint
        $http_endpoint = new SoftLayer_Messaging_Endpoint_Http();
        $http_endpoint->setMethod("POST");
        $http_endpoint->setUrl("http://www.example.com/");
        $http_endpoint->setParams(array('param1' => 'value1'));
        $http_endpoint->setHeaders(array('header1' => 'value1'));
        $http_endpoint->setBody("Example Body");

        $messaging->topic($topicName)->subscription()
            ->setEndpointType('http')
            ->setEndpoint($http_endpoint)
            ->create();
        
        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals("/topics/{$topicName}/subscriptions", $request->getPath());

        // First, we need a queue
        $messaging->queue($queueName)->create();

        // Create a Queue endpoint
        $queue_endpoint = new SoftLayer_Messaging_Endpoint_Queue();
        $queue_endpoint->setQueueName($queueName);

        $messaging->topic($topicName)->subscription()
            ->setEndpointType('queue')
            ->setEndpoint($queue_endpoint)
            ->create();

        if(USE_MOCK == false) {
            sleep(2);
        }

        $subscriptions = $messaging->topic($topicName)->subscriptions();

        $this->assertGreaterThanOrEqual(2, count($subscriptions));

        $messaging->topic($topicName)->delete(true);
        $messaging->queue($queueName)->delete(true);
    }
}

<?php

class Mock
{
    public static function ping()
    {

$body =<<<BODY
OK
BODY;

        $mock = new SoftLayer_Http_Response();
        $mock->setBody($body);

        return $mock;
    }

    public static function authenticate()
    {

$body =<<<BODY
{
    "token":"mocktoken",
    "status":"OK"
}
BODY;

        $mock = new SoftLayer_Http_Response();
        $mock->setStatus(200);
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);

        return $mock;
    }

    public static function queues()
    {

$body = <<<BODY
{
    "item_count":1,
    "items":[
        {
            "name":"queue01", 
            "visibility_interval":30,
            "expiration":604800,
            "tags":[]
        }
    ]
}
BODY;

        $mock = new SoftLayer_Http_Response();
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);

        return $mock;
    }

    public static function queueDetail()
    {
$body = <<<BODY
{
    "name":"testQueueDetail01",
    "visibility_interval":30,
    "expiration":604800,
    "tags":[
        "tag1",
        "tag2"
    ],
    "message_count":0,
    "visible_message_count":0
}
BODY;
        $mock = new SoftLayer_Http_Response();
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);

        return $mock;
    }

    public static function serializedQueue()
    {

$document = <<<DOC
{
    "name":"queue",
    "tags":[
        "tag1",
        "tag2"
    ],
    "visibility_interval":123456789,
    "expiration":987654321,
    "message_count":0,
    "visible_message_count":0
}
DOC;

        return $document;
    }

    public static function serializedTopic()
    {

$document = <<<DOC
{
    "name":"topic",
    "tags":[
        "tag1",
        "tag2"
    ]
}
DOC;

        return $document;
    }

    public static function topics()
    {

$body = <<<BODY
{
    "item_count":1,
    "items":[
        {
            "name":"topic01", 
            "visibility_interval":30,
            "expiration":604800,
            "tags":[]
        }
    ]
}
BODY;

        $mock = new SoftLayer_Http_Response();
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);

        return $mock;
    }

    public static function subscriptions()
    {
$body = <<<BODY
{
    "item_count":1,
    "items":[
        {
            "id":"123abc",
            "endpoint_type":"http",
            "endpoint":{
                "method":"POST",
                "url":"http:\/\/www.example.com\/",
                "params":{
                    "param1":"value1"
                },
                "headers":{
                    "header1":"value1"
                },
                "body":"Example Body"
            }
        },
        {
            "id":"456def",
            "endpoint_type":"queue",
            "endpoint":{
                "queue_name":"testQueueForSubscription"
            }
        }
    ]
}
BODY;

        $mock = new SoftLayer_Http_Response();
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);
        $mock->setStatus(200);

        return $mock;
    }

    public static function messages()
    {
$body = <<<BODY
{
    "item_count":3,
    "items":[
        {
            "id":"123abc",
            "body":"Example 1",
            "visibility_delay":10,
            "visibility_interval":30,
            "expiration":604800
        },
        {
            "id":"456def",
            "body":"Example 2",
            "visibility_delay":10,
            "visibility_interval":30,
            "expiration":604800
        },
        {
            "id":"789ghi",
            "body":"Example 3",
            "visibility_delay":10,
            "visibility_interval":30,
            "expiration":604800
        }
    ]
}
BODY;
        $mock = new SoftLayer_Http_Response();
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);
        $mock->setStatus(200);

        return $mock;
    }

    public static function objectCreated()
    {

$body = <<<BODY
{"message":"Object created"}
BODY;

        $mock = new SoftLayer_Http_Response();
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);
        $mock->setStatus(201);

        return $mock;
    }

    public static function objectDeleted()
    {

$body = <<<BODY
{"message":"Object queued for deletion"}
BODY;

        $mock = new SoftLayer_Http_Response();
        $mock->setHeader('Content-Type', 'application/json');
        $mock->setBody($body);

        return $mock;   
    }
}

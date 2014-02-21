<?php

namespace Tests\SoftLayer;

use SoftLayer\SoftLayer_Messaging;
use SoftLayer\Http\Adapter\SoftLayer_Http_Adapter_Mock;
use Tests\SoftLayer\Mock;

class PingTest extends \PHPUnit_Framework_TestCase
{
    public function testPing()
    {
        $messaging = new SoftLayer_Messaging();

        if(USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::ping());
        }

        $messaging->ping();

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/ping', $request->getPath());
        $this->assertEquals('OK', $response->getBody());
    }
}

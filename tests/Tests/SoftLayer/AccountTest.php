<?php

namespace Test\SoftLayer;

use SoftLayer\Http\Adapter\SoftLayer_Http_Adapter_Mock;
use SoftLayer\SoftLayer_Messaging;
use Tests\SoftLayer\Mock;

class AuthTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthentication()
    {
        $messaging = new SoftLayer_Messaging();

        if (USE_MOCK) {
            $messaging->getClient()->setAdapter(new SoftLayer_Http_Adapter_Mock());
            $messaging->getClient()->getAdapter()->addMockResponse(Mock::authenticate());
        }

        $messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);

        $request = $messaging->getClient()->getRequest();
        $response = $messaging->getClient()->getResponse();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(QUEUE_USERNAME, $request->getHeader('X-Auth-User'));
        $this->assertEquals(QUEUE_API_KEY, $request->getHeader('X-Auth-Key'));

        $this->assertObjectHasAttribute('token', $response->getBody());
        $this->assertObjectHasAttribute('status', $response->getBody());
    }
}

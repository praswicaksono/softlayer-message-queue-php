<?php

namespace SoftLayer\Http\Adapter;

use SoftLayer\Http\Adapter\SoftLayer_Http_Adapter_Interface;
use SoftLayer\Http\SoftLayer_Http_Request;
use SoftLayer\Http\SoftLayer_Http_Response;

class SoftLayer_Http_Adapter_Mock implements SoftLayer_Http_Adapter_Interface
{
    private $responses = array();

    public function addMockResponse($response)
    {
        $this->responses[] = $response;
    }

    public function useMockResponse()
    {
        if (!count($this->responses)) {
            throw new Exception("No mock response available");
        }

        return array_shift($this->responses);
    }

    public function call(SoftLayer_Http_Request &$request, SoftLayer_Http_Response &$response)
    {
        $mock = $this->useMockResponse();

        $response->setStatus($mock->getStatus());
        $response->setPath($mock->getPath());
        $response->setBody($mock->getBody());
        $response->setMethod($mock->getMethod());

        foreach ($mock->getHeaders() as $header => $value) {
            $response->setHeader($header, $value);
        }
    }
}

<?php

namespace SoftLayer\Http\Middleware;

use SoftLayer\Http\SoftLayer_Http_Request;
use SoftLayer\Http\SoftLayer_Http_Response;

class SoftLayer_Http_Middleware_Json implements SoftLayer_Http_Middleware_Interface
{
    public function filterRequest(SoftLayer_Http_Request &$request)
    {
        $request->setHeader('Content-Type', 'application/json;charset=utf-8');
        $request->setBody(json_encode($request->getBody()));
    }

    public function filterResponse(SoftLayer_Http_Response &$response)
    {
        if (stristr($response->getHeader('Content-Type'), 'application/json') !== false) {
            $response->setBody(json_decode($response->getBody()));
        }
    }
}

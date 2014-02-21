<?php

namespace SoftLayer\Http\Middleware;

use SoftLayer\Http\SoftLayer_Http_Request;
use SoftLayer\Http\SoftLayer_Http_Response;

interface SoftLayer_Http_Middleware_Interface
{
    public function filterRequest(SoftLayer_Http_Request &$request);

    public function filterResponse(SoftLayer_Http_Response &$response);
}
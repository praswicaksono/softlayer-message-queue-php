<?php

namespace Softlayer\Http\Middleware;

use Softlayer\Http\SoftLayer_Http_Request;
use Softlayer\Http\SoftLayer_Http_Response;

interface SoftLayer_Http_Middleware_Interface
{
    public function filterRequest(SoftLayer_Http_Request &$request);

    public function filterResponse(SoftLayer_Http_Response &$response);
}
<?php

namespace Softlayer\Messaging\Enpoint;

use Softlayer\Messaging\SoftLayer_Messaging_Entity;

class SoftLayer_Messaging_Endpoint_Http extends SoftLayer_Messaging_Entity
{
    protected static $emit = array('method', 'url', 'params', 'headers', 'body');
    protected static $type = 'http';

    public $method;
    public $url;
    public $params = array();
    public $headers = array();
    public $body = '';

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }
}

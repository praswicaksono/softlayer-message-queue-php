<?php

abstract class SoftLayer_Http_Base
{
    private $method;
    private $path;
    private $body;
    private $headers = array();

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setHeader($header, $value)
    {
        
        $this->headers[$header] = $value;
    }

    public function getHeader($header)
    {
        if(!array_key_exists($header, $this->headers)) {
            return "";
        }

        return $this->headers[$header];
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}

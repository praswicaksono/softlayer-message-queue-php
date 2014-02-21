<?php

namespace Softlayer\Http;

use Softlayer\Http\Adapter\SoftLayer_Http_Adapter_Curl;
use Softlayer\Http\Middleware\SoftLayer_Http_Middleware_Core;
use Softlayer\Http\Middleware\SoftLayer_Http_Middleware_Json;

class SoftLayer_Http_Client
{
    private $baseUrl = '';
    private $middleware = array();
    private $defaultHeaders = array();
    private $adapter;
    private $request;
    private $response;

    public static function getClient()
    {
        $client = new SoftLayer_Http_Client();

        // Default adapter
        $client->setAdapter(new SoftLayer_Http_Adapter_Curl());

        // Middleware
        $client->addMiddleware(new SoftLayer_Http_Middleware_Core());
        $client->addMiddleware(new SoftLayer_Http_Middleware_Json());

        return $client;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function get($path, $options = array())
    {
        $this->call('GET', $path, $options);
        return $this->getResponse();
    }

    public function put($path, $options = array())
    {
        $this->call('PUT', $path, $options);
        return $this->getResponse();
    }

    public function post($path, $options = array())
    {
        $this->call('POST', $path, $options);
        return $this->getResponse();
    }

    public function delete($path, $options = array())
    {
        $this->call('DELETE', $path, $options);
        return $this->getResponse();
    }

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function addMiddleware($middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function setDefaultHeader($header, $value)
    {
        $this->defaultHeaders[$header] = $value;
    }

    private function call($method, $path, $options = array())
    {
        $defaults = array(
            'headers' => array(),
            'params' => array(),
            'body' => ""
        );

        $options = array_merge($defaults, $options);

        // Localize our request and response for the call,
        // which also allows us to pass by reference for our
        // middleware.
        $request = $this->getRequest();
        $response = $this->getResponse();

        $request->setBaseUrl($this->baseUrl);
        $request->setMethod($method);
        $request->setPath($path);
        $request->setParams($options['params']);
        $request->setHeaders($options['headers']);
        $request->setBody($options['body']);

        foreach ($this->defaultHeaders as $header => $value) {
            $request->setHeader($header, $value);
        }

        foreach ($this->middleware as $middleware) {
            $middleware->filterRequest($request);
        }

        $this->adapter->call($request, $response);

        foreach (array_reverse($this->middleware) as $middleware) {
            $middleware->filterResponse($response);
        }

        $this->setRequest($request);
        $this->setResponse($response);
    }

    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new SoftLayer_Http_Request();
        }
        return $this->request;
    }

    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new SoftLayer_Http_Response();
        }
        return $this->response;
    }

    private function setRequest($request)
    {
        $this->request = $request;
    }

    private function setResponse($response)
    {
        $this->response = $response;
    }
}

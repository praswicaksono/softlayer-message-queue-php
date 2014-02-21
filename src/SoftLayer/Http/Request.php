<?php

namespace Softlayer\Http;

class SoftLayer_Http_Request extends SoftLayer_Http_Base
{
    private $params = array();

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }
}

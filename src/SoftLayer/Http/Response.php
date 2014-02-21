<?php

namespace Softlayer\Http;

class SoftLayer_Http_Response extends SoftLayer_Http_Base
{
    private $status;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}

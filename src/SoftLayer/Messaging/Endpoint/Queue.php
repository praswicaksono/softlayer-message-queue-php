<?php

namespace Softlayer\Messaging\Enpoint;

use Softlayer\Messaging\SoftLayer_Messaging_Entity;

class SoftLayer_Messaging_Endpoint_Queue extends SoftLayer_Messaging_Entity
{
    protected static $emit = array('queue_name');
    protected static $type = 'queue';

    public $queue_name;

    public function setQueueName($queue_name)
    {
        $this->queue_name = $queue_name;
        return $this;
    }

    public function getQueueName()
    {
        return $this->queue_name;
    }
}
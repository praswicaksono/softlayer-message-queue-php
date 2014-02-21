<?php

namespace SoftLayer\Messaging;

use SoftLayer\Messaging\SoftLayer_Messaging_Entity;

class SoftLayer_Messaging_Subscription extends SoftLayer_Messaging_Entity
{
    protected static $emit = array('id', 'endpoint_type', 'endpoint');

    protected $id;
    protected $endpoint_type = '';
    protected $endpoint = null;

    public function getId()
    {
        return $this->id;
    }

    public function setEndpointType($endpoint_type)
    {
        $this->endpoint_type = $endpoint_type;
        return $this;
    }

    public function getEndpointType()
    {
        return $this->endpoint_type;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function create()
    {
        $this->getClient()->post(
            "/topics/" . $this->getParent()->getName() . "/subscriptions",
            array('body' => $this->serialize())
        );
        return $this;
    }

    public function delete($id = null)
    {
        $this->getClient()->delete(
            "/" . $this->getParent()->getShortType() . "s/" . $this->getParent()->getName(
            ) . "/subscriptions/" . ($id ? $id : $this->getId())
        );
        return $this;
    }
}
<?php

namespace Softlayer\Messaging;

use Softlayer\Messaging\SoftLayer_Messaging_Entity;

class SoftLayer_Messaging_Message extends SoftLayer_Messaging_Entity
{
    protected static $emit = array('id', 'body', 'fields', 'visibility_interval', 'visibility_delay');

    protected $id;
    protected $body;
    protected $fields = array();
    protected $visibility_interval = 10;
    protected $visibility_delay = 0;

    public function getId()
    {
        return $this->id;
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

    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function addField($field, $value)
    {
        $this->fields[$field] = $value;
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setVisibilityDelay($visibility_delay)
    {
        $this->visibility_delay = $visibility_delay;
        return $this;
    }

    public function getVisibilityDelay()
    {
        return $this->visibility_delay;
    }

    public function setVisibilityInterval($visibility_interval)
    {
        $this->visibility_interval = $visibility_interval;
        return $this;
    }

    public function getVisibilityInterval()
    {
        return $this->visibility_interval;
    }

    public function create()
    {
        $this->getClient()->post(
            "/" . $this->getParent()->getShortType() . "s/" . $this->getParent()->getName() . "/messages",
            array('body' => $this->serialize())
        );
        return $this;
    }

    public function delete($id = null)
    {
        $this->getClient()->delete(
            "/" . $this->getParent()->getShortType() . "s/" . $this->getParent()->getName(
            ) . "/messages/" . ($id ? $id : $this->getId())
        );
        return $this;
    }
}


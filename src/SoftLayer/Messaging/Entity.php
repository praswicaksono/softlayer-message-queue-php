<?php

namespace Softlayer\Messaging;

abstract class SoftLayer_Messaging_Entity
{
    protected static $emit = array();

    private $client;
    private $parent;

    public function getType()
    {
        return strtolower(get_called_class());
    }

    public function getShortType()
    {
        $type = $this->getType();
        $type = explode('_', $type);
        return array_pop($type);
    }

    public function getClient()
    {
        return $this->getRoot()->getClient();
    }

    public function getRoot()
    {
        $parent = $this->getParent();

        if (method_exists($parent, 'getRoot')) {
            return $parent->getRoot();
        }

        return $parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function serialize()
    {
        return (object)array_intersect_key(get_object_vars($this), array_flip(static::$emit));
    }

    public function unserialize($object)
    {
        foreach (static::$emit as $field) {
            if (property_exists($object, $field)) {
                $this->$field = $object->$field;
            }
        }
        return $this;
    }
}

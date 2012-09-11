<?php

class SoftLayer_Messaging_Topic extends SoftLayer_Messaging_Entity
{
    protected static $emit = array('name', 'tags');

    protected $name;
    protected $tags = array();

    public function __construct($name = '')
    {
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function removeTag($tag)
    {
        $index = array_search($tag, $this->tags);

        if($index !== false) {
            array_splice($this->tags, $index, 1);
        }

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function fetch()
    {
        return $this->unserialize($this->getClient()->get("/topics/".$this->getName())->getBody());
    }

    public function create()
    {
        return $this->save();
    }

    public function update()
    {
        return $this->save();
    }

    public function save()
    {
        $this->getClient()->put("/topics/".$this->getName(), array('body' => $this->serialize()));
        return $this;
    }

    public function delete($force = false)
    {
        $this->getClient()->delete("/topics/".$this->getName(), array('params' => array('force' => $force)));
        return $this;
    }

    public function message($body = '')
    {
        $message = new SoftLayer_Messaging_Message();
        $message->setParent($this);
        $message->setBody($body);
        return $message;
    }

    public function subscription($endpoint_type = '')
    {
        $subscription = new SoftLayer_Messaging_Subscription();
        $subscription->setParent($this);
        $subscription->setEndpointType($endpoint_type);
        return $subscription;
    }

    public function subscriptions()
    {
        $subscriptions = array();
        $response = $this->getClient()->get("/topics/".$this->getName()."/subscriptions");

        foreach($response->getBody()->items as $item) {
            $subscription = new SoftLayer_Messaging_Subscription();
            $subscription->setParent($this);
            $subscription->unserialize($item);

            $endpoint = SoftLayer_Messaging_Endpoint::endpointByType($subscription->getEndpointType());
            $endpoint->setParent($subscription);
            $endpoint->unserialize($subscription->getEndpoint());

            $subscription->setEndpoint($endpoint);

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }
}

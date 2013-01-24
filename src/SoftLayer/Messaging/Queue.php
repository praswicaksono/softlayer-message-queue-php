<?php

class SoftLayer_Messaging_Queue extends SoftLayer_Messaging_Entity
{
    protected static $emit = array('name', 'tags', 'visibility_interval', 'expiration', 'message_count', 'visible_message_count');
    protected $fetched = false;

    protected $name;
    protected $tags = array();
    protected $visibility_interval = 10;
    protected $expiration = 604800;
    protected $message_count = 0;
    protected $visible_message_count = 0;

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

    public function setVisibilityInterval($visibility_interval)
    {
        $this->visibility_interval = $visibility_interval;
        return $this;
    }

    public function getVisibilityInterval()
    {
        return $this->visibility_interval;
    }

    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
        return $this;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function getMessageCount()
    {
        if(!$this->fetched) {
            $this->fetch();
        }

        return $this->message_count;
    }

    public function getVisibleMessageCount()
    {
        if(!$this->fetched) {
            $this->fetch();
        }

        return $this->visible_message_count;
    }

    public function fetch()
    {
        $this->fetched = true;
        return $this->unserialize($this->getClient()->get("/queues/".$this->getName())->getBody());
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
        $this->getClient()->put("/queues/".$this->getName(), array('body' => $this->serialize()));
        return $this;
    }

    public function delete($force = false)
    {
        $this->getClient()->delete("/queues/".$this->getName(), array('params' => array('force' => $force)));
        return $this;
    }

    public function message($body = '')
    {
        $message = new SoftLayer_Messaging_Message();
        $message->setParent($this);
        $message->setBody($body);
        return $message;
    }

    public function messages($batch = 1)
    {
        $messages = array();
        $response = $this->getClient()->get("/queues/".$this->getName()."/messages", array('params' => array('batch' => $batch)));

        foreach($response->getBody()->items as $item) {
            $message = new SoftLayer_Messaging_Message();
            $message->setParent($this);
            $message->unserialize($item);

            $messages[] = $message;
        }

        return $messages;
    }
}

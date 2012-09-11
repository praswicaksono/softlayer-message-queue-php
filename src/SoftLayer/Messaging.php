<?php

SoftLayer_Messaging::__register_autoloader();
SoftLayer_Messaging::__load_endpoints();

class SoftLayer_Messaging
{
    public static $autoloaded = false;
    public static $endpoints = array();
    public static $endpoints_config = '/config.json';

    private $endpoint = "";
    private $token = null;
    private $account;
    private $client;

    public function SoftLayer_Messaging($endpoint = "dal05", $private = false)
    {
        $this->endpoint = "https://".self::$endpoints[$endpoint][($private?'private':'public')]."/v1";
    }

    public function ping()
    {
        $this->getClient()->setBaseUrl($this->endpoint);
        return $this->getClient()->get('/ping')->getBody();
    }

    public function authenticate($account, $user, $key)
    {
        $this->getClient()->setBaseUrl("{$this->endpoint}/{$account}");
        $this->getClient()->post("/auth", array(
            'headers' => array(
                'X-Auth-User' => $user,
                'X-Auth-Key' => $key
            )
        ));

        $response = $this->getClient()->getResponse();

        if($response->getStatus() == 200) {
            $this->getClient()->setDefaultHeader('X-Auth-Token', $response->getBody()->token);
            return true;
        }

        return false;
    }
    
    public function stats($last = 'hour')
    {
        return $this->getClient()->get('/stats/'.$last)->getBody();
    }

    public function queue($name = '')
    {
        $queue = new SoftLayer_Messaging_Queue();
        $queue->setParent($this);
        $queue->setName($name);
        return $queue;
    }

    public function queues($tags = array())
    {
        $queues = array();
        $query = "/queues";
        
        if($tags) {
            $query .= "?tags=".implode(',', $tags);
        }

        $response = $this->getClient()->get($query);

        foreach($response->getBody()->items as $item) {
            $queue = new SoftLayer_Messaging_Queue();
            $queue->setParent($this);
            $queue->unserialize($item);

            $queues[] = $queue;
        }

        return $queues;
    }

    public function topic($name = '')
    {
        $topic = new SoftLayer_Messaging_Topic();
        $topic->setParent($this);
        $topic->setName($name);
        return $topic;
    }

    public function topics($tags = array())
    {
        $topics = array();
        $query = "/topics";
        
        if($tags) {
            $query .= "?tags=".implode(',', $tags);
        }

        $response = $this->getClient()->get($query);

        foreach($response->getBody()->items as $item) {
            $topic = new SoftLayer_Messaging_Topic();
            $topic->setParent($this);
            $topic->unserialize($item);

            $topics[] = $topic;
        }

        return $topics;
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

    public function getClient()
    {
        if(!$this->client) {
            $this->client = SoftLayer_Http_Client::getClient();
        }
        return $this->client;
    }

    public static function __load_endpoints()
    {
        $root = dirname(__FILE__);
        $config = $root . self::$endpoints_config;

        // If we've already loaded this, break out early.
        if(count(self::$endpoints) > 0) {
            return;
        }

        if(!file_exists($config)) {
            die("An endpoints config.json file is required.");
        }

        $json = json_decode(file_get_contents($config), true);
        self::$endpoints = $json['endpoints'];
    }

    public static function __register_autoloader()
    {
        if(!SoftLayer_Messaging::$autoloaded) {
            SoftLayer_Messaging::$autoloaded = true;
            spl_autoload_register("SoftLayer_Messaging::__autoloader");
        }
    }

    public static function __autoloader($class)
    {
        $root = dirname(dirname(__FILE__));

        $path = explode('_', $class);
        $path = implode(DIRECTORY_SEPARATOR, $path);
        $path = $root . DIRECTORY_SEPARATOR . $path . ".php";

        if(file_exists($path)) {
            include_once $path;
        }
    }
}

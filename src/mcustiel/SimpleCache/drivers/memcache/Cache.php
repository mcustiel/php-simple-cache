<?php
namespace mcustiel\SimpleCache\drivers\memcache;

use mcustiel\SimpleCache\interfaces\CacheInterface;

class Cache implements CacheInterface
{
    private $connection;

    public function __construct()
    {
        $this->connection = new \Memcache();
    }

    /**
     */
    public function init(\stdClass $initData = null)
    {
        return $this->connection->connect($initData->host, $initData->port, $initData->timeout);
    }

    /**
     */
    public function exists($key)
    {
        return $this->connection->get($key) !== false;
    }

    /**
     */
    public function get($key)
    {
        return $this->connection->get($key);
    }

    /**
     */
    public function set($key, $value, \stdClass $options = null)
    {
        return $this->connection->set($key, $value, $options->flags, $options->expire);
    }
}

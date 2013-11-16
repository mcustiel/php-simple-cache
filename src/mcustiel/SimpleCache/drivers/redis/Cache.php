<?php
namespace mcustiel\SimpleCache\drivers\redis;

use mcustiel\SimpleCache\interfaces\CacheInterface;
use mcustiel\SimpleCache\drivers\redis\exceptions\SimpleCacheRedisException;

class Cache implements CacheInterface
{
    private $connection;

    public function __construct()
    {
        $this->connection = new \Redis();
    }

    /**
     */
    public function init(\stdClass $initData = null)
    {
        $this->connection->connect($initData->host, $initData->port, $initData->timeout);
        $this->authenticate($initData->password);
        $this->selectDatabase($initData->database);
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
        return unserialize($this->connection->get($key));
    }

    /**
     */
    public function set($key, $value, \stdClass $options = null)
    {
        return $this->connection->set($key, serialize($value), $options->timeLife);
    }

    /**
     *
     * @param unknown $password
     * @throws SimpleCacheRedisException
     */
    private function authenticate($password)
    {
        if ($password && ! $this->connection->auth($password)) {
            throw new SimpleCacheRedisException(SimpleCacheRedisException::AUTHENTICATION_FAILED);
        }
    }

    /**
     *
     * @param unknown $database
     */
    private function selectDatabase($database)
    {
        if ($database) {
            $this->connection->select($database);
        }
    }
}

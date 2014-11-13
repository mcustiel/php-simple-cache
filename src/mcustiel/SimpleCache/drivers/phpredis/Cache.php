<?php
namespace mcustiel\SimpleCache\drivers\redis;

use mcustiel\SimpleCache\interfaces\CacheInterface;
use mcustiel\SimpleCache\drivers\redis\exceptions\RedisAuthenticationException;

class Cache implements CacheInterface
{
    private $connection;

    public function __construct(\Redis $redisConnection)
    {
        $this->connection = $redisConnection === null ?
            new \Redis() :
            $redisConnection;
    }

    /**
     */
    public function init(\stdClass $initData = null)
    {
        $this->connection->connect(
            $initData->host,
            $initData->port,
            $initData->timeout
        );
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
        return $this->connection->psetex($key, serialize($value), $options->timeToLive);
    }

    public function delete($key)
    {
        $this->connection->delete($key);
    }

    /**
     *
     * @param string $password
     * @throws RedisAuthenticationException
     */
    private function authenticate($password)
    {
        if (! $this->connection->auth($password)) {
            throw new RedisAuthenticationException();
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

<?php
namespace mcustiel\SimpleCache\drivers\phpredis;

use mcustiel\SimpleCache\interfaces\CacheInterface;
use mcustiel\SimpleCache\drivers\Key;
use mcustiel\SimpleCache\drivers\phpredis\exceptions\RedisAuthenticationException;

class Cache implements CacheInterface
{
    const DEFAULT_HOST = 'localhost';

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
        if ($initData === null) {
            $this->connection->connect(self::DEFAULT_HOST);
        } else {
            $this->connection->connect(
                isset($initData->host) ? $initData->host : self::DEFAULT_HOST,
                isset($initData->port) ? $initData->port : null,
                isset($initData->timeoutInSeconds) ? $initData->timeoutInSeconds : null,
                null,
                isset($initData->retryDelayInMillis) ? $initData->retryDelayInMillis : null
            );
            $this->authenticate($initData->password);
            $this->selectDatabase($initData->database);
        }
    }

    /**
     */
    public function get(Key $key)
    {
        $value = $this->connection->get($key);
        return $value === false? null : unserialize($value);
    }

    /**
     */
    public function set(Key $key, $value, \stdClass $options = null)
    {
        return $this->connection->psetex(
            $key,
            serialize($value),
            isset($options->timeToLive) ? $options->timeToLive : null
        );
    }

    public function delete(Key $key)
    {
        $this->connection->delete($key->getKeyName());
    }

    /**
     *
     * @param string $password
     * @throws RedisAuthenticationException
     */
    private function authenticate($password)
    {
        if (isset($password)) {
            if (! $this->connection->auth($password)) {
                throw new RedisAuthenticationException();
            }
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

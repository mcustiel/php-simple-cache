<?php
namespace Mcustiel\SimpleCache\Drivers\phpredis;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Types\Key;
use Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisAuthenticationException;

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
            if (isset($initData->password)) {
                $this->authenticate($initData->password);
            }
            if (isset($initData->database)) {
                $this->connection->select($initData->database);
            }
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
        if (! $this->connection->auth($password)) {
            throw new RedisAuthenticationException();
        }
    }
}

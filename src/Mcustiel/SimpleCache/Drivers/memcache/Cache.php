<?php
namespace Mcustiel\SimpleCache\Drivers\memcache;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Types\Key;

class Cache implements CacheInterface
{
    private $connection;

    public function __construct(\Memcache $memcacheConnection = null)
    {
        $this->connection = $memcacheConnection === null ?
            new \Memcache() :
            $memcacheConnection;
    }

    /**
     */
    public function init(\stdClass $initData = null)
    {
        if ($initData === null) {
            $this->connection->connect();
        } else {
            $this->connection->connect(
                isset($initData->host) ? $initData->host : null,
                isset($initData->port) ? $initData->port : null,
                isset($initData->timeoutInSeconds) ? $initData->timeoutInSeconds : null
            );
        }
    }

    /**
     */
    public function get(Key $key)
    {
        $value = $this->connection->get($key->getKeyName());

        return $value === false ? null : $value;
    }

    /**
     */
    public function set(Key $key, $value, \stdClass $options = null)
    {
        return $this->connection->set(
            $key->getKeyName(),
            $value,
            $options !== null? $options->flags : null,
            isset($options->timeToLive) ? floor($options->timeToLive / 1000) : 0
        );
    }

    /**
     * (non-PHPdoc)
     * @see \Mcustiel\SimpleCache\interfaces\CacheInterface::delete()
     */
    public function delete(Key $key)
    {
        $this->connection->delete($key->getKeyName());
    }
}

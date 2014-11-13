<?php
namespace mcustiel\SimpleCache\drivers\memcache;

use mcustiel\SimpleCache\interfaces\CacheInterface;
use mcustiel\SimpleCache\drivers\Key;

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
        return $this->connection->connect(
            $initData->host,
            $initData->port,
            $initData->timeout);
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
            $options->flags,
            isset($options->timeToLive) ? round($options->timeToLive / 1000) : 0
        );
    }

    /**
     * (non-PHPdoc)
     * @see \mcustiel\SimpleCache\interfaces\CacheInterface::delete()
     */
    public function delete(Key $key)
    {
        $this->connection->delete($key->getKeyName());
    }
}

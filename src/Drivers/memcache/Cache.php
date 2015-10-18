<?php
/**
 * This file is part of php-simple-cache.
 *
 * php-simple-cache is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-simple-cache is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-simple-cache.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Mcustiel\SimpleCache\Drivers\memcache;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Drivers\memcache\Exceptions\MemcacheConnectionException;
use Mcustiel\SimpleCache\Interfaces\KeyInterface;

class Cache implements CacheInterface
{
    /**
     * @var \Memcache
     */
    private $connection;

    /**
     * @param \Memcache $memcacheConnection optional A Memcache object.
     */
    public function __construct(\Memcache $memcacheConnection = null)
    {
        $this->connection = $memcacheConnection === null ?
            new \Memcache() :
            $memcacheConnection;
    }

    /**
     * {@inheritDoc}
     * Connects to Memcache.
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::init()
     */
    public function init(\stdClass $initData = null)
    {
        if ($initData === null) {
            $this->connection->connect();
        } else {
            $this->openConnection($initData);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::get()
     */
    public function get(KeyInterface $key)
    {
        $value = $this->connection->get($key->getKeyName());

        return $value === false ? null : $value;
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::set()
     */
    public function set(KeyInterface $key, $value, $ttlInMillis)
    {
        return $this->connection->set(
            $key->getKeyName(),
            $value,
            null,
            time() + floor($ttlInMillis / 1000)
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::delete()
     */
    public function delete(KeyInterface $key)
    {
        $this->connection->delete($key->getKeyName());
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::finish()
     */
    public function finish()
    {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Class destructor. Calls finish method.
     */
    public function __destruct()
    {
        $this->finish();
    }

    /**
     * Opens a connection to memcached server.
     *
     * @param \stdClass $initData
     */
    private function openConnection(\stdClass $initData)
    {
        $connectionOptions = $this->parseConnectionData($initData);
        if (isset($this->persistent) && (boolean) $this->persistent) {
            $this->persistentConnect($connectionOptions);
        } else {
            $this->notPersistentConnect($connectionOptions);
        }
    }

    /**
     *
     * @param \stdClass $connectionOptions
     *
     * @throws \Mcustiel\SimpleCache\Drivers\memcache\Exceptions\MemcacheConnectionException
     */
    private function notPersistentConnect(\stdClass $connectionOptions)
    {
        if (!$this->connection->connect(
            $connectionOptions->host,
            $connectionOptions->port,
            $connectionOptions->timeout
        )) {
            throw new MemcacheConnectionException(
                "Can't connect to memcache server with config: "
                . var_export($connectionOptions, true)
            );
        };
    }

    /**
      * @param \stdClass $connectionOptions
      *
      * @throws \Mcustiel\SimpleCache\Drivers\memcache\Exceptions\MemcacheConnectionException
      */
    private function persistentConnect(\stdClass $connectionOptions)
    {
        if (!$this->connection->pconnect(
            $connectionOptions->host,
            $connectionOptions->port,
            $connectionOptions->timeout
        )) {
            throw new MemcacheConnectionException(
                "Can't connect to memcache server with config: "
                . var_export($connectionOptions, true)
            );
        };
    }

    /**
     * Fixes memcached configuration.
     *
     * @param \stdClass $initData
     * @return \stdClass
     */
    private function parseConnectionData(\stdClass $initData)
    {
        $return = new \stdClass;
        $return->host = isset($initData->host) ? $initData->host : self::DEFAULT_HOST;
        $return->port = isset($initData->port) ? $initData->port : null;
        $return->timeout = isset($initData->timeoutInSeconds) ? $initData->timeoutInSeconds : null;

        return $return;
    }
}

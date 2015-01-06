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
use Mcustiel\SimpleCache\Types\Key;
use Mcustiel\SimpleCache\Drivers\memcache\Exceptions\MemcacheConnectionException;

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
            $this->openConnection($initData);
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
    public function set(Key $key, $value, $ttlInMillis)
    {
        return $this->connection->set(
            $key->getKeyName(),
            $value,
            null,
            time() + floor($ttlInMillis / 1000)
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

    public function finish()
    {
        $this->connection->close();
    }

    private function openConnection(\stdClass $initData)
    {
        $connectionOptions = $this->parseConnectionData($initData);
        if (isset($this->persistent) && (boolean) $this->persistent) {
            $this->persistentConnect($connectionOptions);
        } else {
            $this->notPersistentConnect($connectionOptions);
        }
    }

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

    private function parseConnectionData(\stdClass $initData)
    {
        $return = new \stdClass;
        $return->host = isset($initData->host) ? $initData->host : self::DEFAULT_HOST;
        $return->port = isset($initData->port) ? $initData->port : null;
        $return->timeout = isset($initData->timeoutInSeconds) ? $initData->timeoutInSeconds : null;

        return $return;
    }
}

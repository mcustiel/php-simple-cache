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
namespace Mcustiel\SimpleCache\Drivers\phpredis;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisAuthenticationException;
use Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException;
use Mcustiel\SimpleCache\Interfaces\KeyInterface;

class Cache implements CacheInterface
{
    const DEFAULT_HOST = 'localhost';

    /**
     * @var \Redis
     */
    private $connection;

    /**
     * @param \Redis $redisConnection A Redis object
     */
    public function __construct(\Redis $redisConnection = null)
    {
        $this->connection = $redisConnection === null ? new \Redis() : $redisConnection;
    }

    /**
     * {@inheritDoc}
     * Opens a connection to redis server.
     *
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::init()
     */
    public function init(\stdClass $initData = null)
    {
        try {
            if ($initData === null) {
                $this->connection->connect(self::DEFAULT_HOST);
            } else {
                $this->openConnection($initData);
            }
        } catch (\RedisException $e) {
            throw new RedisConnectionException("Redis driver exception was thrown.", $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::get()
     */
    public function get(KeyInterface $key)
    {
        $value = $this->connection->get($key);
        return $value === false? null : unserialize($value);
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::set()
     */
    public function set(KeyInterface $key, $value, $ttlInMillis)
    {
        return $this->connection->psetex(
            $key,
            $ttlInMillis,
            serialize($value)
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
     * @param string $password
     *
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisAuthenticationException
     */
    private function authenticate($password)
    {
        if (! $this->connection->auth($password)) {
            throw new RedisAuthenticationException();
        }
    }

    /**
     * @param \stdClass $initData
     *
     * @return \stdClass
     */
    private function parseConnectionData(\stdClass $initData)
    {
        $return = new \stdClass;
        $return->host = isset($initData->host) ? $initData->host : self::DEFAULT_HOST;
        $return->port = isset($initData->port) ? $initData->port : null;
        $return->timeout = isset($initData->timeoutInSeconds)
            ? $initData->timeoutInSeconds : null;
        $return->retryDelay = isset($initData->retryDelayInMillis)
            ? $initData->retryDelayInMillis : null;

        return $return;
    }

    /**
     * @param \stdClass $connectionOptions
     *
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     */
    private function notPersistentConnect(\stdClass $connectionOptions)
    {
        if (!$this->connection->connect(
            $connectionOptions->host,
            $connectionOptions->port,
            $connectionOptions->timeout,
            null,
            $connectionOptions->retryDelay
        )) {
            throw new RedisConnectionException(
                "Can't connect to redis server with config: " . var_export($connectionOptions, true)
            );
        };
    }

    /**
     * @param \stdClass $connectionOptions
     * @param string $persistentId
     *
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     */
    private function persistentConnect(\stdClass $connectionOptions, $persistentId)
    {
        if (!$this->connection->pconnect(
            $connectionOptions->host,
            $connectionOptions->port,
            $connectionOptions->timeout,
            $persistentId,
            $connectionOptions->retryDelay
        )) {
            throw new RedisConnectionException(
                "Can't connect to redis server with config: " . var_export($connectionOptions, true)
            );
        };
    }

    /**
     * @param \stdClass $initData
     *
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     */
    private function openConnection(\stdClass $initData)
    {
        $connectionOptions = $this->parseConnectionData($initData);
        if (isset($initData->persistentId) && !empty($initData->persistentId)) {
            $this->persistentConnect($connectionOptions, persistentId);
        } else {
            $this->notPersistentConnect($connectionOptions);
        }
        $this->executePostConnectionOptions($initData);
    }

    /**
     * @param \stdClass $initData
     *
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisAuthenticationException
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     */
    private function executePostConnectionOptions(\stdClass $initData)
    {
        if (isset($initData->password)) {
            $this->authenticate($initData->password);
        }
        if (isset($initData->database)) {
            $this->selectDatabase($initData->database);
        }
    }

    /**
     * @param unknown $database
     *
     * @throws \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     */
    private function selectDatabase($database)
    {
        if (!is_integer($database) || $database < 0) {
            throw new RedisConnectionException(
                "Can't select database '{$database}'. Should be a natural number."
            );
        }
        $this->connection->select($database);
    }
}

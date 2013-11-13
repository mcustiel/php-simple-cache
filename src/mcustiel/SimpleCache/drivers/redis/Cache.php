<?php
namespace mcustiel\SimpleCache\drivers\redis;

use mcustiel\SimpleCache\interfaces\CacheInterface;

class Cache implements CacheInterface
{
    private $connection;

    public function __construct()
    {
        $this->connection = new \SQLite3(':memory:');
    }

    /**
     */
    public function init(\stdClass $initData = null)
    {
        $this->connection->exec('CREATE TABLE collection (key STRING UNIQUE,  value STRING)');
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
    public function set($key, $value,\stdClass $options = null)
    {
        return $this->connection->set($key, $value, $options->timeLife);
    }

    private function authenticate($password)
    {
        if ($password && ! $this->connection->auth($password)) {
            // throw exception
        }
    }

    private function selectDatabase($database)
    {
        if ($database) {
            $this->connection->select($database);
        }
    }
}

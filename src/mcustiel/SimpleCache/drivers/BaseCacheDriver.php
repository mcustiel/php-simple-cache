<?php
namespace mcustiel\SimpleCache\drivers;

use mcustiel\SimpleCache\drivers\Key;
use mcustiel\SimpleCache\interfaces\CacheInterface;

abstract class BaseCacheDriver implements CacheInterface
{
    /**
     * Keys data
     * @var \mcustiel\SimpleCache\drivers\Key[]
     */
    private $keysMap = [];

    private $defaultTimeout;

    public function __construct($defaultTimeToLive = -1)
    {
        $this->defaultTimeout =
            $defaultTimeToLive < 0 ? 0 : (integer) $defaultTimeToLive;
    }

    public function setKey(Key $key, $timeToLive = null)
    {
        if ($timeToLive === null || $timeToLive < 0) {
            $timeToLive = $this->defaultTimeout;
        }
        $this->keysMap[$key] = new CacheLogRegister($key,
            microtime() + $timeToLive * 1000);
    }

    public function isKeyExpired($keyName)
    {
        if (isset($this->keysMap[$keyName])) {
            return $this->keysMap[$keyName]->getTimeout() < microtime();
        }

        return false;
    }

    public function deleteKey($keyName)
    {
        unset($this->keysMap[$keyName]);
    }

    abstract public function init(\stdClass $initData = null);

    abstract public function get(Key $key);

    abstract public function set(Key $key, $value, \stdClass $options = null);

    abstract public function delete(Key $key);
}
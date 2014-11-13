<?php
namespace mcustiel\SimpleCache\drivers;

class CacheLogRegister
{
    private $key;
    private $timeout;

    public function __construt(Key $key, $timeout)
    {
        $this->key = $key;
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }
}
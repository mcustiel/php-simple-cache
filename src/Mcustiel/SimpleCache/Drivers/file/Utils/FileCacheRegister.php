<?php
namespace Mcustiel\SimpleCache\Types;

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
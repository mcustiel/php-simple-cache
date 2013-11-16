<?php
namespace mcustiel\SimpleCache\interfaces;

interface CacheInterface
{
    public function init(\stdClass $initData = null);

    public function exists($key);

    public function get($key);

    public function set($key, $value, \stdClass $options = null);
}

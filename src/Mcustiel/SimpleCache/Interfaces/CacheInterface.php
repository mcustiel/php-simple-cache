<?php
namespace Mcustiel\SimpleCache\Interfaces;

use Mcustiel\SimpleCache\Types\Key;

interface CacheInterface
{
    function init(\stdClass $initData = null);

    function get(Key $key);

    function set(Key $key, $value, $ttlInMillis);

    function delete(Key $key);
}

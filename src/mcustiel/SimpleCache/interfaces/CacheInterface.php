<?php
namespace mcustiel\SimpleCache\interfaces;

use mcustiel\SimpleCache\drivers\Key;

interface CacheInterface
{
    function init(\stdClass $initData = null);

    function get(Key $key);

    function set(Key $key, $value, \stdClass $options = null);

    function delete(Key $key);
}

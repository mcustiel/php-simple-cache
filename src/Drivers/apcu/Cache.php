<?php
namespace Mcustiel\SimpleCache\Drivers\apcu;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Types\Key;

class Cache implements CacheInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::init()
     */
    public function init(\stdClass $initData = null)
    {}

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::get()
     */
    public function get(Key $key)
    {
        $ok = true;
        $value = apc_fetch($key->__toString(), $ok);

        return $ok ? $value : null;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::set()
     */
    public function set(Key $key, $value, $ttlInMillis)
    {
        return apc_store($key->__toString(), $value, (int) $ttlInMillis / 1000);
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::delete()
     */
    public function delete(Key $key)
    {
        return apc_delete($key->__toString());
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::finish()
     */
    public function finish()
    {}
}

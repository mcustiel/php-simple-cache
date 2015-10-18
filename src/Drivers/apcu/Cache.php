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
namespace Mcustiel\SimpleCache\Drivers\apcu;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Interfaces\KeyInterface;

class Cache implements CacheInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::init()
     */
    public function init(\stdClass $initData = null)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::get()
     */
    public function get(KeyInterface $key)
    {
        $success = true;
        $value = apc_fetch($key->__toString(), $success);

        return $success ? $value : null;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::set()
     */
    public function set(KeyInterface $key, $value, $ttlInMillis)
    {
        return apc_store($key->__toString(), $value, (int) $ttlInMillis / 1000);
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::delete()
     */
    public function delete(KeyInterface $key)
    {
        return apc_delete($key->__toString());
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::finish()
     */
    public function finish()
    {
    }
}

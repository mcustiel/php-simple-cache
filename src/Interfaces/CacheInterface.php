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
namespace Mcustiel\SimpleCache\Interfaces;

interface CacheInterface
{
    /**
     * Initializes the cache manager with specific required data.
     * This method should be called once after object construction.
     *
     * @param \stdClass $initData optional Data needed to init the driver.
     *
     * @throws \Mcustiel\SimpleCache\Exceptions\PhpSimpleCacheException On initialization error.
     */
    public function init(\stdClass $initData = null);

    /**
     * Returns the stored value associated with the given key. If it does not exists, returns null.
     *
     * @param KeyInterface $key
     */
    public function get(KeyInterface $key);

    /**
     * @param KeyInterface $key         A key to identified the stored value
     * @param mixed        $value       The value to store in cache
     * @param integer      $ttlInMillis Cache time to live in milliseconds
     *
     * @return boolean True on success, false otherwise
     */
    public function set(KeyInterface $key, $value, $ttlInMillis);

    /**
     * Deletes the stored value in the cache associated with the given key.
     *
     * @param KeyInterface $key The stored key
     */
    public function delete(KeyInterface $key);

    /**
     * Terminates any initialized data set up in the init method.
     */
    public function finish();
}

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
namespace Mcustiel\SimpleCache;

use Mcustiel\SimpleCache\Exceptions\UnknownDriverException;

class SimpleCache
{
    /**
     * @param string $cacheManager
     *
     * @return \Mcustiel\SimpleCache\interfaces\CacheInterface
     * @throws \Mcustiel\SimpleCache\Exceptions\UnknownDriverException
     */
    public function getCacheManager($cacheManager)
    {
        $class = $this->getFullManagerPath($cacheManager);
        if (! class_exists($class)) {
            throw new UnknownDriverException($cacheManager);
        }
        return new $class;
    }

    /**
     * @param string $cacheManager
     *
     * @return string
     */
    public function getFullManagerPath($cacheManager)
    {
        return "\\Mcustiel\\SimpleCache\\Drivers\\{$cacheManager}\\Cache";
    }
}

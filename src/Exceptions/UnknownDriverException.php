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
namespace Mcustiel\SimpleCache\Exceptions;

class UnknownDriverException extends PhpSimpleCacheException
{
    const DEFAULT_MESSAGE = 'Unknown cache manager driver: %s';
    const EXCEPTION_CODE = 1000;

    public function __construct($cacheName, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, $cacheName),
            self::EXCEPTION_CODE,
            $previous
        );
    }
}

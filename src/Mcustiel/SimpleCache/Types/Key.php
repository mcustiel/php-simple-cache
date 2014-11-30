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
namespace Mcustiel\SimpleCache\Types;

class Key
{
    const INVALID_CHARS_REGEXP = '/[^a-z\-_0-9.]/i';
    const KEY_PREFIX = '_PSC-key_';

    private $keyName;

    public function __construct($keyName, $timeoutInMilliseconds = 0)
    {
        $this->keyName = $this->fixKeyChars($keyName);
    }

    public function getKeyName()
    {
        return $this->keyName;
    }

    public function __toString()
    {
        return $this->keyName;
    }

    /**
     * Fixes the string to remove unallowed characters
     *
     * @param string $key
     * @return string
     */
    private function fixKeyChars($key)
    {
        return preg_replace(self::INVALID_CHARS_REGEXP, '', $key);
    }
}

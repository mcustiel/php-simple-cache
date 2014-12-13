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
namespace Mcustiel\SimpleCache\Drivers\file\Utils;

use Mcustiel\SimpleCache\Types\Key;

class FileCacheRegister implements \Serializable
{
    private $data;
    private $expiration;

    public function __construct($data, $expiration)
    {
        $this->data = $data;
        $this->expiration = $expiration;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function getData()
    {
        return $this->data;
    }

    public function serialize()
    {
        return serialize([$this->data, $this->expiration]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->data = $data[0];
        $this->expiration = $data[1];
    }
}

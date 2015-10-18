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
namespace Mcustiel\SimpleCache\Drivers\file;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Drivers\file\Exceptions\FilesCachePathNotAssigned;
use Mcustiel\SimpleCache\Drivers\file\Utils\FileService;
use Mcustiel\SimpleCache\Drivers\file\Utils\FileCacheRegister;
use Mcustiel\SimpleCache\Interfaces\KeyInterface;

class Cache implements CacheInterface
{
    private $fileService;

    /**
     * @param \Mcustiel\SimpleCache\Drivers\file\Utils\FileService $fileService
     */
    public function __construct(FileService $fileService = null)
    {
        $this->fileService = $fileService === null ? new FileService() : $fileService;
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::init()
     */
    public function init(\stdClass $initData = null)
    {
        if (!isset($initData->filesPath)) {
            throw new FilesCachePathNotAssigned();
        }
        $this->fileService->setFilesPath(
            rtrim($initData->filesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::get()
     */
    public function get(KeyInterface $key)
    {
        if ($this->exists($key)) {
            $register = unserialize($this->fileService->getFrom($key->getKeyName()));
            if ($register->getExpiration() == 0 || $register->getExpiration() >= microtime()) {
                return $register->getData();
            }
            $this->delete($key);
        }
        return null;
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::set()
     */
    public function set(KeyInterface $key, $value, $ttlInMillis)
    {
        $this->fileService->saveIn(
            $key->getKeyName(),
            serialize(new FileCacheRegister(
                $value,
                $ttlInMillis == 0 ? 0 : microtime() + $ttlInMillis * 1000
            ))
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::delete()
     */
    public function delete(KeyInterface $key)
    {
        if ($this->exists($key)) {
            $this->fileService->delete($key->getKeyName());
        }
    }

    /**
     * {@inheritDoc}
     * @see \Mcustiel\SimpleCache\Interfaces\CacheInterface::finish()
     */
    public function finish()
    {
    }

    /**
     * @param \Mcustiel\SimpleCache\Types\Key $key
     * @return boolean
     */
    private function exists(KeyInterface $key)
    {
        return $this->fileService->exists($key->getKeyName());
    }
}

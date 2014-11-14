<?php
namespace Mcustiel\SimpleCache\Drivers\file;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Types\Key;
use Mcustiel\SimpleCache\Drivers\file\Exceptions\FilesCachePathNotAssigned;
use Mcustiel\SimpleCache\Drivers\file\Utils\FileService;
use Mcustiel\SimpleCache\Drivers\file\Utils\FileCacheRegister;

class Cache implements CacheInterface
{
    private $fileService;

    public function __construct(FileService $fileService = null)
    {
        $this->fileService = $fileService === null ? new FileService() : $fileService;
    }

    /**
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
     */
    public function get(Key $key)
    {
        if ($this->exists($key)) {
            $register = unserialize($this->fileService->getFrom($key->getKeyName()));
            if ($register->getExpiration() >= microtime()) {
                return $register->getData();
            }
            $this->delete($key);
        }
        return null;
    }

    /**
     */
    public function set(Key $key, $value, $ttlInMillis)
    {
        $this->fileService->saveIn(
            $key->getKeyName(),
            serialize(new FileCacheRegister(
                $value,
                microtime() + $ttlInMillis * 1000
            ))
        );
    }

    /**
     * (non-PHPdoc)
     * @see \Mcustiel\SimpleCache\Drivers\BaseCacheDriver::delete()
     */
    public function delete(Key $key)
    {
        if ($this->exists($key)) {
            $this->fileService->delete($key->getKeyName());
        }
    }

    /**
     */
    private function exists(Key $key)
    {
        return $this->fileService->exists($key->getKeyName());
    }
}

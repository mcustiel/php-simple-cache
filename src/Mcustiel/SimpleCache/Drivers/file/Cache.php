<?php
namespace Mcustiel\SimpleCache\Drivers\file;

use Mcustiel\SimpleCache\Interfaces\CacheInterface;
use Mcustiel\SimpleCache\Types\Key;
use Mcustiel\SimpleCache\Drivers\file\Exceptions\FilesCachePathNotAssigned;
use Mcustiel\SimpleCache\Drivers\file\Utils\FileService;

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
        if (! $this->exists($key)) {
            return null;
        }

        return unserialize($this->fileService->getFrom($key->getKeyName()));
    }

    /**
     */
    public function set(Key $key, $value, \stdClass $options = null)
    {
        $this->fileService->saveIn(
            $key->getKeyName(),
            serialize($value)
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

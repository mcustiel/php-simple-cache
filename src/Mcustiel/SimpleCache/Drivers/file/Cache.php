<?php
namespace Mcustiel\SimpleCache\Drivers\file;

use Mcustiel\SimpleCache\interfaces\CacheInterface;
use Mcustiel\SimpleCache\Types\Key;
use Mcustiel\SimpleCache\Drivers\BaseCacheDriver;
use Mcustiel\SimpleCache\Drivers\file\Exceptions\FilesCachePathNotAssigned;
use Mcustiel\SimpleCache\Drivers\file\Utils\FileService;

class Cache extends BaseCacheDriver
{
    /**
     * Path to directory where cache files are stored.
     * @var string
     */
    private $path;

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
        $this->path = rtrim($initData->filesPath, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR;
    }

    /**
     */
    public function get(Key $key)
    {
        $key = $key->getKeyName();
        if (! $this->exists($key)) {
            return null;
        }

        return unserialize($this->fileService->getFrom($this->getFileName($key)));
    }

    /**
     */
    public function set(Key $key, $value, \stdClass $options = null)
    {
        parent::setKey(
            $key,
            isset($options->timeToLive) ? $options->timeToLive : null
        );
        $this->fileService->saveIn(
            $this->getFileName($key->getKeyName()),
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
            parent::deleteKey($key->getKeyName());
            $this->fileService->delete($this->getFileName($key));
        }
    }

    /**
     *
     * @param unknown $key
     * @return string
     */
    private function getFileName($key)
    {
        return "{$this->path}{$key}";
    }

    /**
     */
    private function exists($key)
    {
        return $this->fileService->exists($this->getFileName($key));
    }
}

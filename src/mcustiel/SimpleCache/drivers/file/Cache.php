<?php
namespace mcustiel\SimpleCache\drivers\file;

use mcustiel\SimpleCache\interfaces\CacheInterface;
use mcustiel\SimpleCache\drivers\Key;
use mcustiel\SimpleCache\drivers\BaseCacheDriver;
use mcustiel\SimpleCache\drivers\file\exceptions\FilesCachePathNotAssigned;
use mcustiel\SimpleCache\drivers\file\utils\FileService;

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
        if (!isset($initData->filePath)) {
            throw new FilesCachePathNotAssigned();
        }
        $this->path = rtrim($initData->filePath, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR;

        return true;
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
     * @see \mcustiel\SimpleCache\drivers\BaseCacheDriver::delete()
     */
    public function delete(Key $key)
    {
        if ($this->exists($key)) {
            parent::delete($key->getKeyName());
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

<?php
namespace mcustiel\SimpleCache\drivers\file;

use mcustiel\SimpleCache\interfaces\CacheInterface;

class Cache implements CacheInterface
{

    const INVALID_KEY_CHARS_REGEXP = '/[‰-z]/';
    private $path;

    /**
     */
    public function init(\stdClass $initData = null)
    {
        $this->path = rtrim($initData->filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return true;
    }

    /**
     */
    public function exists($key)
    {
        $key = $this->fixKeyChars($key);
        return file_exists($this->getFileName($key));
    }

    /**
     */
    public function get($key)
    {
        $key = $this->fixKeyChars($key);
        return unserialize(file_get_contents($this->getFileName($key)));
    }

    /**
     */
    public function set($key, $value)
    {
        $key = $this->fixKeyChars($key);
        file_put_contents($this->getFileName($key), serialize($value));
    }

    protected function fixKeyChars($key)
    {
        return preg_replace(self::INVALID_KEY_CHARS_REGEXP, '', $key);
    }

    protected function getFileName($key)
    {
        return "{$this->path}{$key}";
    }
}

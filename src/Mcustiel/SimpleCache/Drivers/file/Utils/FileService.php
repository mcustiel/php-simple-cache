<?php
namespace Mcustiel\SimpleCache\Drivers\file\Utils;

class FileService
{
    /**
     * Path to directory where cache files are stored.
     *
     * @var string
     */
    private $filesPath;

    public function __construct($filesPath = null)
    {
        $this->filesPath = empty($filesPath) ?
            '/tmp/php-simple-config/cache/' :
            rtrim($filesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function setFilesPath($filesPath)
    {
        $this->filesPath = rtrim($filesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function saveIn($name, $content)
    {
        file_put_contents($this->getFullPath($name), $content);
    }

    public function getFrom($name)
    {
        return file_get_contents($this->getFullPath($name));
    }

    public function exists($name)
    {
        return file_exists($this->getFullPath($name));
    }

    public function delete($name)
    {
        return unlink($this->getFullPath($name));
    }

    private function getFullPath($fileName)
    {
        return "{$this->filesPath}{$fileName}";
    }
}

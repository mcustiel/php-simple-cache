<?php
namespace Mcustiel\SimpleCache\Drivers\file\Utils;

class FileService
{
    public function saveIn($name, $content)
    {
        file_put_contents($name, $content);
    }

    public function getFrom($name)
    {
        return file_get_contents($name);
    }

    public function exists($name)
    {
        return file_exists($name);
    }

    public function delete($name)
    {
        return unlink($name);
    }
}

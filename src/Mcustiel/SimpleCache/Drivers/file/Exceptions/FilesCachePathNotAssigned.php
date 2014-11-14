<?php
namespace Mcustiel\SimpleCache\Drivers\file\Exceptions;

use Mcustiel\SimpleCache\Exceptions\SimpleCacheException;

class FilesCachePathNotAssigned extends SimpleCacheException
{
    const DEFAULT_MESSAGE = 'The path to the directory that stores cacheFiles is not set';
    const DEFAULT_CODE = 2000;

    public function __construct(\Exception $previous = null)
    {
        parent::__construct(self::DEFAULT_MESSAGE, self::DEFAULT_CODE, $previous);
    }
}
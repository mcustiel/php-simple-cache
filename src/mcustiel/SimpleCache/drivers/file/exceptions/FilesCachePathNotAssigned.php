<?php

namespace mcustiel\SimpleCache\drivers\file\exceptions;

class FilesCachePathNotAssigned extends \Exception
{
    const DEFAULT_MESSAGE = 'The path to the directory that stores cacheFiles is not set';
    const DEFAULT_CODE = 2000;

    public function __construct(\Exception $previous = null)
    {
        parent::__construct(self::DEFAULT_MESSAGE, self::DEFAULT_MESSAGE, $previous);
    }
}
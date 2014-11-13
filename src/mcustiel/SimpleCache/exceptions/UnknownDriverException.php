<?php
namespace mcustiel\SimpleCache\exceptions;

class UnknownDriverException extends SimpleCacheException
{
    const DEFAULT_MESSAGE = 'Unknown cache manager driver: %s';
    const EXCEPTION_CODE = 1000;

    public function __construct($cacheName, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(self::DEFAULT_MESSAGE, $cacheName),
            self::EXCEPTION_CODE,
            $previous
        );
    }
}

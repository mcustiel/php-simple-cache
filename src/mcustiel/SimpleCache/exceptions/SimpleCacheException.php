<?php
namespace mcustiel\SimpleCache\exceptions;

class SimpleCacheException extends \Exception
{
    const KEY_IS_EMPTY = 1;
    const INVALID_VALUE = 2;
    const INIT_FAILED = 3;

    protected static $exceptions = array(
        self::KEY_IS_EMPTY => 'The key used to identify the cached value is empty',
        self::INVALID_VALUE => 'You are trying to cache an invalid value',
        self::INIT_FAILED => 'Failed to initialize the cache system'
    );

    public function __construct($exceptionCode, \Exception $previous = null)
    {
        parent::__construct(self::$exceptions[$exceptionCode], $exceptionCode, $previous);
    }
}

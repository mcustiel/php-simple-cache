<?php
namespace mcustiel\SimpleCache\drivers\redis\exceptions;

use mcustiel\SimpleCache\exceptions\SimpleCacheException;

class SimpleCacheRedisException extends SimpleCacheException
{
    const AUTHENTICATION_FAILED = 1;

    protected static $exceptions = array(
        self::AUTHENTICATION_FAILED => 'Authentication failed'
    );

    public function __construct($exceptionCode, \Exception $previous = null)
    {
        parent::__construct(self::$exceptions[$exceptionCode], $exceptionCode, $previous);
    }
}

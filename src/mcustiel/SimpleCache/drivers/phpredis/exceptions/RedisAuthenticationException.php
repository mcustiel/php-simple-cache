<?php
namespace mcustiel\SimpleCache\drivers\redis\exceptions;

use mcustiel\SimpleCache\exceptions\SimpleCacheException;

class RedisAuthenticationException extends SimpleCacheException
{
    const DEFAULT_MESSAGE = 'Authentication failed';
    const DEFAULT_CODE = 2100;

    public function __construct(\Exception $previous = null)
    {
        parent::__construct(
            self::DEFAULT_MESSAGE,
            self::EXCEPTION_CODE,
            $previous
        );
    }
}

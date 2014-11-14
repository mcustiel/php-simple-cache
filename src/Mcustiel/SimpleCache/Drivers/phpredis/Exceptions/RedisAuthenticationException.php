<?php
namespace Mcustiel\SimpleCache\Drivers\phpredis\Exceptions;

use Mcustiel\SimpleCache\Exceptions\SimpleCacheException;

class RedisAuthenticationException extends SimpleCacheException
{
    const DEFAULT_MESSAGE = 'Authentication failed';
    const DEFAULT_CODE = 2100;

    public function __construct(\Exception $previous = null)
    {
        parent::__construct(
            self::DEFAULT_MESSAGE,
            self::DEFAULT_CODE,
            $previous
        );
    }
}

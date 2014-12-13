<?php
/**
 * This file is part of php-simple-cache.
 *
 * php-simple-cache is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-simple-cache is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-simple-cache.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Unit\SimpleCache\Drivers\phpredis;

use Mcustiel\SimpleCache\Drivers\phpredis\Cache;
use Mcustiel\SimpleCache\Types\Key;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    const CACHED_DATA = 'This is the data in the cache';

    private $cache;
    private $redis;
    private $key;

    public function setUp()
    {
        $this->key = new Key('potato');

        $this->redis = $this->getMockBuilder('\\Redis')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache = new Cache($this->redis);
    }

    public function testInitWithoutOptions()
    {
        $this->redis
            ->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('localhost'));
        $this->redis
            ->expects($this->never())
            ->method('auth');
        $this->redis
            ->expects($this->never())
            ->method('select');
        $this->cache->init();
    }

    public function testInitWithDefaultOptions()
    {
        $this->redis
            ->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('localhost'), null, null, null, null)
            ->will($this->returnValue(true));
        $this->redis
            ->expects($this->never())
            ->method('auth');
        $this->redis
            ->expects($this->never())
            ->method('select');
        $this->cache->init(new \stdClass());
    }

    public function testInitWithDefinedOptions()
    {
        $options = new \stdClass();
        $options->host = 'host';
        $options->port = 1234;
        $options->timeoutInSeconds = 5;
        $options->retryDelayInMillis = 500;
        $options->password = 'passwd';
        $options->database = 0;

        $this->redis
            ->expects($this->once())
            ->method('connect')
            ->with(
                $this->equalTo($options->host),
                $this->equalTo($options->port),
                $this->equalTo($options->timeoutInSeconds),
                null,
                $this->equalTo($options->retryDelayInMillis)
            )
            ->will($this->returnValue(true));
        $this->redis
            ->expects($this->once())
            ->method('auth')
            ->with($this->equalTo($options->password))
            ->will($this->returnValue(true));
        $this->redis
            ->expects($this->once())
            ->method('select')
            ->with($this->equalTo(0));
        $this->cache->init($options);
    }

    /**
     * @expectedException        \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     * @expectedExceptionMessage Can't select database 'invalid'. Should be a natural number.
     */
    public function testDatabaseInvalidString()
    {
        $options = new \stdClass();
        $options->host = 'host';
        $options->database = 'invalid';

        $this->redis
            ->expects($this->once())
            ->method('connect')
            ->with($this->equalTo($options->host), null, null, null, null)
            ->will($this->returnValue(true));
        $this->redis
            ->expects($this->never())
            ->method('select');
        $this->cache->init($options);
    }

    /**
     * @expectedException        \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     * @expectedExceptionMessage Can't select database '3.5'. Should be a natural number.
     */
    public function testDatabaseInvalidFloat()
    {
        $options = new \stdClass();
        $options->host = 'localhost';
        $options->database = 3.5;

        $this->redis
            ->expects($this->once())
            ->method('connect')
            ->with($this->equalTo($options->host), null, null, null, null)
            ->will($this->returnValue(true));
        $this->redis
            ->expects($this->never())
            ->method('select');
        $this->cache->init($options);
    }

    /**
     * @expectedException        \Mcustiel\SimpleCache\Drivers\phpredis\Exceptions\RedisConnectionException
     * @expectedExceptionMessage Redis driver exception was thrown
     */
    public function testRedisThrowsExceptionOnConnect()
    {
        $this->redis
            ->expects($this->once())
            ->method('connect')
            ->with($this->equalTo(Cache::DEFAULT_HOST))
            ->will($this->throwException(new \RedisException("Redis failed")));
        $this->cache->init();
    }

    public function testDatabaseIsNumericString()
    {
        $options = new \stdClass();
        $options->host = 'host';
        $options->database = '2';

        $this->redis
            ->expects($this->once())
            ->method('connect')
            ->with($this->equalTo($options->host), null, null, null, null)
            ->will($this->returnValue(true));
        $this->redis
            ->expects($this->once())
            ->method('select')
            ->with($this->equalTo(2));
        $this->cache->init($options);
    }

    public function testIfReturnsNullWhenKeyDoesNotExist()
    {
        $this->redis
            ->method('get')
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(false));
        $this->assertNull($this->cache->get($this->key));
    }

    public function testIfReturnsNullWhenKeyExists()
    {
        $this->redis
            ->method('get')
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(serialize(self::CACHED_DATA)));
        $this->assertEquals(self::CACHED_DATA, $this->cache->get($this->key));
    }

    public function testIfSetsValueWithTimeToLiveValue()
    {
        $this->redis
            ->expects($this->once())
            ->method('psetex')
            ->with(
                $this->equalTo($this->key->getKeyName()),
                $this->equalTo(5000),
                $this->equalTo(serialize(self::CACHED_DATA))
            );
        $this->cache->set($this->key, self::CACHED_DATA, 5000);
    }

    public function testIfRedisDeleteIsCalled()
    {
        $this->redis
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($this->key->getKeyName()));
        $this->cache->delete($this->key);
    }
}

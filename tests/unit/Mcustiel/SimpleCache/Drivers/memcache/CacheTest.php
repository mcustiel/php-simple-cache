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
namespace Unit\SimpleCache\Drivers\memcache;

use Mcustiel\SimpleCache\Drivers\memcache\Cache;
use Mcustiel\SimpleCache\Types\Key;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    const CACHED_DATA = 'This is the data in the cache';

    private $cache;
    private $memcache;
    private $key;

    public function setUp()
    {
        $this->key = new Key('potato');

        $this->memcache = $this->getMockBuilder('\\Memcache')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache = new Cache($this->memcache);
    }

    public function testIfConnectIsCalledOnInit()
    {
        $data = new \stdClass();
        $data->host = 'localhost';
        $data->port = 1234;
        $data->timeoutInSeconds = 1000;

        $this->memcache
            ->expects($this->once())
            ->method('connect')
            ->with(
                $this->equalTo('localhost'),
                $this->equalTo(1234),
                $this->equalTo(1000)
            );
        $this->cache->init($data);
    }

    public function testIfConnectIsCalledOnInitWithoutConnectionOptions()
    {
        $this->memcache
            ->expects($this->once())
            ->method('connect');
        $this->cache->init();
    }

    public function testIfReturnsNullWhenMemcacheReturnsFalse()
    {
        $this->memcache
            ->method('get')
            ->with($this->key->getKeyName())
            ->will($this->returnValue(false));
        $this->assertNull($this->cache->get($this->key));
    }

    public function testIfReturnsValueFromMemcache()
    {
        $this->memcache
            ->method('get')
            ->with($this->key->getKeyName())
            ->will($this->returnValue(self::CACHED_DATA));
        $this->assertEquals(self::CACHED_DATA, $this->cache->get($this->key));
    }

    public function testIfSetsValueInMemcacheWithTimeToLive()
    {
        $this->memcache
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($this->key->getKeyName()),
                $this->equalTo(self::CACHED_DATA),
                null,
                $this->greaterThanOrEqual(time() + 1)
            );
        $this->cache->set($this->key, self::CACHED_DATA, 1000);
    }

    public function testIfCallsDeleteOnMemcache()
    {
        $this->memcache
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($this->key->getKeyName()));
        $this->cache->delete($this->key, self::CACHED_DATA);
    }
}

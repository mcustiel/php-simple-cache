<?php
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
        $options = new \stdClass;
        $options->flags = ['aFlag ', true];
        $options->timeToLive = 1000;
        $this->memcache
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo($this->key->getKeyName()),
                $this->equalTo(self::CACHED_DATA),
                $this->equalTo($options->flags),
                $this->equalTo(1)
            );
        $this->cache->set($this->key, self::CACHED_DATA, $options);
    }

    public function testIfUsesDefaultValueWhenOptionsOmmited()
    {
        $this->memcache
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo($this->key->getKeyName()),
                $this->equalTo(self::CACHED_DATA),
                $this->identicalTo(null),
                $this->equalTo(0)
            );
        $this->cache->set($this->key, self::CACHED_DATA);
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

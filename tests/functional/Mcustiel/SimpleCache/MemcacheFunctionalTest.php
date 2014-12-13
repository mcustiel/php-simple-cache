<?php
namespace Functional\SimpleCache;

use \Mcustiel\SimpleCache\Drivers\memcache\Cache as MemcacheCache;
use Mcustiel\SimpleCache\Types\Key;

class MemcacheFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mcustiel\SimpleCache\Drivers\memcache\Cache
     */
    private $cache;

    public function setUp()
    {
        $this->cache = new MemcacheCache();
    }

    public function tearDown()
    {
        $this->cache->finish();
    }

    public function testIfCacheInitsSettingOptionsInDriver()
    {
        $this->initMemcacheCacheFromTestsConfig();
        $this->assertKeysCanBeWrittenAndRead();
    }

    public function testIfCacheInitsWithOpenConnection()
    {
        $raw = new \Memcache();
        $raw->connect(MEMCACHE_HOST, MEMCACHE_PORT, MEMCACHE_TIMEOUT_SECONDS);
        $this->cache = new MemcacheCache($raw);
        $this->assertKeysCanBeWrittenAndRead();
    }

    public function testDelete()
    {
        $this->initMemcacheCacheFromTestsConfig();
        $key = new Key("keyToBeDeleted");
        $this->cache->set($key, "aValue", 3600000);
        $value = $this->cache->get($key);
        $this->assertEquals("aValue", $value);
        $this->cache->delete($key);
        $value = $this->cache->get($key);
        $this->assertNull($value);
    }

    private function assertKeysCanBeWrittenAndRead()
    {
        $key = new Key("aKey");
        $this->cache->set($key, "aValue", 1000);
        $value = $this->cache->get($key);
        $this->assertEquals("aValue", $value);
    }

    private function initMemcacheCacheFromTestsConfig()
    {
        $config = new \stdClass();
        $config->host = MEMCACHE_HOST;
        $config->port = MEMCACHE_PORT;
        $config->timeoutInSeconds = MEMCACHE_TIMEOUT_SECONDS;
        $this->cache->init($config);
    }
}

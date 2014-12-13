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
        $this->initPhpRedisCacheFromTestsConfig();
        $this->assertKeysCanBeWrittenAndRead();
    }

    public function testIfCacheInitsWithOpenConnection()
    {
        $redis = new \Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT, REDIS_TIMEOUT_SECONDS);
        $this->cache = new RedisCache($redis);
        $this->assertKeysCanBeWrittenAndRead();
    }

    public function testDelete()
    {
        $this->initPhpRedisCacheFromTestsConfig();
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

    private function initPhpRedisCacheFromTestsConfig()
    {
        $config = new \stdClass();
        $config->host = REDIS_HOST;
        $config->port = REDIS_PORT;
        $config->timeoutInSeconds = REDIS_TIMEOUT_SECONDS;
        $this->cache->init($config);
    }
}

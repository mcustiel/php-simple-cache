<?php
namespace Unit\SimpleCache\drivers\file;

use mcustiel\SimpleCache\drivers\file\Cache;
use mcustiel\SimpleCache\drivers\Key;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    const FILES_PATH = '/tmp/testDir';
    const CACHED_DATA = 'This is the data in the cache';

    private $cache;
    private $fileService;
    private $key;

    public function setUp()
    {
        $this->key = new Key('potato');

        $this->fileService = $this->getMock('mcustiel\\SimpleCache\\drivers\\file\\utils\\FileService');
        $this->cache = new Cache($this->fileService);

        $initData = new \stdClass();
        $initData->filePath = self::FILES_PATH;
        $this->cache->init($initData);
    }

    /**
     * @expectedException \mcustiel\SimpleCache\drivers\file\exceptions\FilesCachePathNotAssigned
     * @expectedExceptionMessage The path to the directory that stores cacheFiles is not set
     */
    public function testIfInitFailsWhenNotInitDataSupplied()
    {
        $this->cache->init();
    }

    /**
     * @expectedException \mcustiel\SimpleCache\drivers\file\exceptions\FilesCachePathNotAssigned
     * @expectedExceptionMessage The path to the directory that stores cacheFiles is not set
     */
    public function testIfInitFailsWhenFilePathIsNotSet()
    {
        $data = new \stdClass();
        $data->noImportant = 'value';
        $this->cache->init($data);
    }

    public function testIfGetReturnsNullWhenKeyDoesNotExist()
    {
        $this->fileService
            ->method('exists')
            ->with($this->equalTo(self::FILES_PATH . '/' . $this->key->getKeyName()))
            ->will($this->returnValue(false));

        $this->assertNull($this->cache->get($this->key));
    }

    public function testIfGetReturnsSavedValue()
    {
        $this->fileService
            ->method('exists')
            ->with($this->equalTo(self::FILES_PATH . '/' . $this->key->getKeyName()))
            ->will($this->returnValue(true));

        $this->fileService
            ->method('getFrom')
            ->with($this->equalTo(self::FILES_PATH . '/' . $this->key->getKeyName()))
            ->will($this->returnValue(serialize(self::CACHED_DATA)));

        $this->assertEquals(self::CACHED_DATA, $this->cache->get($this->key));
    }

    public function testIfSetCallsServicesSaveIn()
    {
        $this->fileService
            ->expects($this->once())
            ->method('saveIn')
            ->with(
                $this->equalTo(self::FILES_PATH . '/' . $this->key->getKeyName()),
                $this->equalTo(serialize(self::CACHED_DATA))
            );
        $this->cache->set($this->key, self::CACHED_DATA);
    }

    public function testIfDeleteIsNotCalledWhenKeyDoesNotExist()
    {
        $this->fileService
            ->method('exists')
            ->with($this->equalTo(self::FILES_PATH . '/' . $this->key->getKeyName()))
            ->will($this->returnValue(false));

        $this->fileService
            ->expects($this->never())
            ->method('delete');

        $this->cache->delete($this->key);
    }

    public function testIfDeleteIsCalledWhenKeyExist()
    {
        $this->fileService
            ->method('exists')
            ->with($this->equalTo(self::FILES_PATH . '/' . $this->key->getKeyName()))
            ->will($this->returnValue(true));

        $this->fileService
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(self::FILES_PATH . '/' . $this->key->getKeyName()));

        $this->cache->delete($this->key);
    }
}

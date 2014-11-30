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
namespace Unit\SimpleCache\Drivers\file;

use Mcustiel\SimpleCache\Drivers\file\Cache;
use Mcustiel\SimpleCache\Types\Key;
use Mcustiel\SimpleCache\Drivers\file\Utils\FileCacheRegister;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    const CACHED_DATA = 'This is the data in the cache';

    private $cache;
    private $fileService;
    private $key;

    public function setUp()
    {
        $this->key = new Key('potato');

        $this->fileService = $this
            ->getMockBuilder('Mcustiel\\SimpleCache\\Drivers\\file\\Utils\\FileService')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache = new Cache($this->fileService);
    }

    /**
     * @expectedException \Mcustiel\SimpleCache\Drivers\file\exceptions\FilesCachePathNotAssigned
     * @expectedExceptionMessage The path to the directory that stores cacheFiles is not set
     */
    public function testIfInitFailsWhenNotInitDataSupplied()
    {
        $this->cache->init();
    }

    /**
     * @expectedException \Mcustiel\SimpleCache\Drivers\file\exceptions\FilesCachePathNotAssigned
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
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(false));

        $this->assertNull($this->cache->get($this->key));
    }

    public function testGetWithExpiredKey()
    {
        $this->fileService
            ->method('exists')
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(true));

        $this->fileService
            ->method('getFrom')
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(
                serialize(new FileCacheRegister(self::CACHED_DATA, microtime() - 5000000))
            ));

        $this->fileService
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($this->key->getKeyName()));

        $this->assertNull($this->cache->get($this->key));
    }

    public function testIfGetReturnsSavedValue()
    {
        $this->fileService
            ->method('exists')
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(true));

        $this->fileService
            ->method('getFrom')
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(serialize(
                new FileCacheRegister(self::CACHED_DATA, microtime() + 5000000))
            ));

        $this->assertEquals(self::CACHED_DATA, $this->cache->get($this->key));
    }

    public function testIfSetCallsServicesSaveIn()
    {
        $this->fileService
            ->expects($this->once())
            ->method('saveIn')
            ->with(
                $this->equalTo($this->key->getKeyName()),
                $this->callback(function($value) {
                    $data = unserialize($value);
                    return $data->getData() == self::CACHED_DATA
                        && $data->getExpiration() > microtime() / 1000;
                })
            );
        $this->cache->set($this->key, self::CACHED_DATA, 5000);
    }

    public function testIfDeleteIsNotCalledWhenKeyDoesNotExist()
    {
        $this->fileService
            ->method('exists')
            ->with($this->equalTo($this->key->getKeyName()))
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
            ->with($this->equalTo($this->key->getKeyName()))
            ->will($this->returnValue(true));

        $this->fileService
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($this->key->getKeyName()));

        $this->cache->delete($this->key);
    }
}

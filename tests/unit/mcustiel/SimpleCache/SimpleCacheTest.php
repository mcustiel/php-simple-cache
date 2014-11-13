<?php
namespace Unit\SimpleCache;

use mcustiel\SimpleCache\SimpleCache;

class SimpleCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Unit under test
     *
     * @var mcustiel\SimpleCache\SimpleCache
     */
    private $simpleCache;

    public function setUp()
    {
        $this->simpleCache = new SimpleCache();
    }

    public function testIfReturnedPathIsCorrect()
    {
        $path = $this->simpleCache->getFullManagerPath('file');
        $this->assertEquals("\\mcustiel\\SimpleCache\\drivers\\file\\Cache", $path);
    }

    public function testIfReturnsTheManagerForTheRequiredDriver()
    {
        $driver = $this->simpleCache->getCacheManager('file');
        $this->assertInstanceOf("\\mcustiel\\SimpleCache\\drivers\\file\\Cache", $driver);
    }

    /**
     * @expectedException \mcustiel\SimpleCache\exceptions\UnknownDriverException
     * @expectedExceptionMessage Unknown cache manager driver: potato
     */
    public function testIfThrowsExceptionWhenDriverDoesNotExist()
    {
        $this->simpleCache->getCacheManager('potato');
    }
}

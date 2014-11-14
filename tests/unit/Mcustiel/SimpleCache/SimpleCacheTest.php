<?php
namespace Unit\SimpleCache;

use Mcustiel\SimpleCache\SimpleCache;

class SimpleCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Unit under test
     *
     * @var Mcustiel\SimpleCache\SimpleCache
     */
    private $simpleCache;

    public function setUp()
    {
        $this->simpleCache = new SimpleCache();
    }

    public function testIfReturnedPathIsCorrect()
    {
        $path = $this->simpleCache->getFullManagerPath('file');
        $this->assertEquals("\\Mcustiel\\SimpleCache\\Drivers\\file\\Cache", $path);
    }

    public function testIfReturnsTheManagerForTheRequiredDriver()
    {
        $driver = $this->simpleCache->getCacheManager('file');
        $this->assertInstanceOf("\\Mcustiel\\SimpleCache\\Drivers\\file\\Cache", $driver);
    }

    /**
     * @expectedException \Mcustiel\SimpleCache\exceptions\UnknownDriverException
     * @expectedExceptionMessage Unknown cache manager driver: potato
     */
    public function testIfThrowsExceptionWhenDriverDoesNotExist()
    {
        $this->simpleCache->getCacheManager('potato');
    }
}

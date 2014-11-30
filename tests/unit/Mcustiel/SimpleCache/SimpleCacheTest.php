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

<?php
namespace Mcustiel\SimpleCache;

use Mcustiel\SimpleCache\Exceptions\UnknownDriverException;

class SimpleCache
{
    /**
     *
     * @param unknown $cacheManager
     * @return \Mcustiel\SimpleCache\interfaces\CacheInterface
     */
    public function getCacheManager($cacheManager)
    {
        $class = $this->getFullManagerPath($cacheManager);
        if (! class_exists($class)) {
            throw new UnknownDriverException($cacheManager);
        }
        return new $class;
    }

    public function getFullManagerPath($cacheManager)
    {
        return "\\Mcustiel\\SimpleCache\\Drivers\\{$cacheManager}\\Cache";
    }
}

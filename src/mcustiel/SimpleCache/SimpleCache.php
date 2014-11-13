<?php
namespace mcustiel\SimpleCache;

use mcustiel\SimpleCache\exceptions\UnknownDriverException;
class SimpleCache
{
    /**
     *
     * @param unknown $cacheManager
     * @return \mcustiel\SimpleCache\interfaces\CacheInterface
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
        return "\\mcustiel\\SimpleCache\\drivers\\{$cacheManager}\\Cache";
    }
}

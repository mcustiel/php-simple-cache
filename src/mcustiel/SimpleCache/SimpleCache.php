<?php
namespace mcustiel\SimpleCache;

class SimpleCache
{
    public function getCacheManager($cacheManager)
    {
        $class = "\\mcustiel\\SimpleCache\\drivers\\{$cacheManager}\\Cache";
        return new $class;
    }
}

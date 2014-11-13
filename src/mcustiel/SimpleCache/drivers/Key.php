<?php
namespace mcustiel\SimpleCache\drivers;

class Key
{
    const INVALID_CHARS_REGEXP = '/[^a-z\-_0-9.]/i';
    const KEY_PREFIX = '_PSC-key_';

    private $keyName;

    public function __construct($keyName, $timeoutInMilliseconds = 0)
    {
        $this->keyName = $this->fixKeyChars($keyName);
    }

    public function getKeyName()
    {
        return $this->keyName;
    }

    public function __toString()
    {
        return $this->keyName;
    }

    /**
     * Fixes the string to remove unallowed characters
     *
     * @param string $key
     * @return string
     */
    private function fixKeyChars($key)
    {
        return preg_replace(self::INVALID_CHARS_REGEXP, '', $key);
    }
}

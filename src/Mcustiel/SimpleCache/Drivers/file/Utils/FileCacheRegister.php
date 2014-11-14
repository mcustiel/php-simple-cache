<?php
namespace Mcustiel\SimpleCache\Drivers\file\Utils;

use Mcustiel\SimpleCache\Types\Key;

class FileCacheRegister implements \Serializable
{
    private $data;
    private $expiration;

    public function __construct($data, $expiration)
    {
        $this->data = $data;
        $this->expiration = $expiration;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function getData()
    {
        return $this->data;
    }

    public function serialize()
    {
        return serialize([$this->data, $this->expiration]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->data = $data[0];
        $this->expiration = $data[1];
    }
}
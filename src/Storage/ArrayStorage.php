<?php

namespace Betacie\Google\Storage;

class ArrayStorage implements StorageInterface
{

    private $parameters;

    public function __construct()
    {
        $this->parameters = array();
    }

    public function track($method, $parameters)
    {
        if (!array_key_exists($method, $this->parameters)) {
            $this->parameters[$method] = array();
        }

        $this->parameters[$method][] = $parameters;
    }

    public function read()
    {
        return $this->parameters;
    }

    public function purge()
    {
        $this->parameters = array();
    }
    
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    public function clear($key)
    {
        unset($this->parameters[$key]);
    }
}
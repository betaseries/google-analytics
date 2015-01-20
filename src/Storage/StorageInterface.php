<?php

namespace Betacie\Google\Storage;

interface StorageInterface
{

    public function track($method, $parameters);

    public function read();

    public function purge();

    public function get($key, $default = null);

    public function clear($key);
}
<?php

namespace Betacie\Google\Session;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

class TrackingBag implements SessionBagInterface
{

    /**
     * @var string
     */
    private $name = 'tracking';

    /**
     * @var string
     */
    private $storageKey;

    /**
     * Tracking container
     *
     * @var array
     */
    private $values = array();

    public function __construct($storageKey = '_google_tracking')
    {
        $this->storageKey = $storageKey;
    }

    public function clear()
    {
        $this->values = array();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStorageKey()
    {
        return $this->storageKey;
    }

    public function initialize(array &$values)
    {
        $this->values = &$values;
    }

    public function add($type, $parameters)
    {
        $this->values[$type][] = $parameters;
    }

    public function all()
    {
        return $this->values;
    }

    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->values) ? $this->values[$key] : $default;
    }

    public function remove($key)
    {
        unset($this->values[$key]);
    }
}
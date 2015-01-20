<?php

namespace Betacie\Google\Storage;

use Betacie\Google\Session\TrackingBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorage implements StorageInterface
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $trackingName;

    public function __construct(SessionInterface $session, TrackingBag $tracking = null)
    {
        $this->session      = $session;
        $tracking           = $tracking ? : new TrackingBag();
        $this->trackingName = $tracking->getName();

        $this->session->registerBag($tracking);
    }

    public function purge()
    {
        $this->getTrackingBag()->clear();
    }

    public function read()
    {
        return $this->getTrackingBag()->all();
    }

    public function track($method, $parameters)
    {
        $this->getTrackingBag()->add($method, $parameters);
    }

    public function get($key, $default = null)
    {
        return $this->getTrackingBag()->get($key, $default);
    }

    public function clear($key)
    {
        $this->getTrackingBag()->remove($key);
    }

    private function getTrackingBag()
    {
        return $this->session->getBag($this->trackingName);
    }

}
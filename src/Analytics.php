<?php

namespace Betacie\Google;

use Betacie\Google\Tracker\TrackerInterface;

class Analytics
{
    protected $trackingId;
    protected $tracker;

    public function __construct($trackingId, TrackerInterface $tracker)
    {
        if (!$trackingId) {
            throw new \InvalidArgumentException('Parameter "%betacie_google.analytics.tracking_id%" has not been set.');
        }

        $this->trackingId = $trackingId;
        $this->tracker = $tracker;
    }

    public function getTracker()
    {
        return $this->tracker;
    }

    public function render()
    {
        return $this->renderGlobalObject()
            .$this->renderCreateAccount()
            .$this->renderPageView()
            .$this->tracker->render()
        ;
    }

    public function renderGlobalObject()
    {
        return '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,"script","//www.google-analytics.com/analytics.js","ga");'.PHP_EOL;
    }

    public function renderCreateAccount()
    {
        return sprintf('ga("create", "%s", "auto");%s', $this->trackingId, PHP_EOL);
    }

    public function renderPageView()
    {
        return 'ga("send", "pageview");'.PHP_EOL;
    }
}

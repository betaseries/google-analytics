<?php

namespace Betacie\Google\Tracker;

class AggregateTracker implements TrackerInterface
{

    private $trackers = array();

    public function addTracker(TrackerInterface $tracker)
    {
        $this->trackers[] = $tracker;
    }

    public function render()
    {
        $return = '';

        foreach ($this->trackers as $tracker) {
            $return .= $tracker->render();
        }

        return $return;
    }

}
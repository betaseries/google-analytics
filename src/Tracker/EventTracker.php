<?php

namespace Betacie\Google\Tracker;

use Betacie\Google\Storage\StorageInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTracker implements TrackerInterface
{

    /**
     * @var StorageInterface
     */
    private $storage;

    function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function trackEvent($parameters)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'category', 'action',
        ))->setDefaults(array(
            'label'          => null,
            'value'          => null,
            'noninteraction' => null,
        ));
        $parameters = $resolver->resolve($parameters);

        $this->storage->track('_trackEvent', $parameters);
    }

    public function render()
    {
        $return = '';

        foreach ($this->storage->get('_trackEvent', array()) as $event) {
            $return .= $this->renderEvent($event);
        }

        $this->storage->clear('_trackEvent');

        return $return;
    }

    public function renderEvent(array $event)
    {
        return sprintf(
            'ga("send","event","%s","%s",%s);%s', 
            $event['category'],
            $event['action'],
            json_encode($this->getEventOptions($event)),
            PHP_EOL
        );
    }

    protected function getEventOptions(array $event)
    {
        $parameters = [];

        if (isset($event['label'])) {
            $parameters['eventLabel'] = $event['label'];
        }

        if (isset($event['value'])) {
            $parameters['eventValue'] = $event['value'];
        }

        if (isset($event['noninteraction'])) {
            $parameters['nonInteraction'] = $event['noninteraction'];
        }

        return $parameters;
    }
}
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
            'label'          => '',
            'value'          => '',
            'noninteraction' => '',
        ))->setNormalizers(array(
            'noninteraction' => function(Options $options, $value) {
                if ($value) {
                    return 'true';
                }

                return 'false';
            }
        ));
        $parameters = $resolver->resolve($parameters);

        $this->storage->track('_trackEvent', $parameters);
    }

    public function render()
    {
        $return = '';

        foreach ($this->storage->get('_trackEvent', array()) as $event) {
            $return .= sprintf("_gaq.push(['_trackEvent', '%s', '%s', '%s', %s, %s]);", $event['category'], $event['action'], $event['label'], $event['value'], $event['noninteraction']);
        }

        $this->storage->clear('_trackEvent');

        return $return;
    }

}
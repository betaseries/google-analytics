<?php

namespace Betacie\Google\Tracker;

use Betacie\Google\Storage\StorageInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcommerceTracker implements TrackerInterface
{

    /**
     * @var StorageInterface
     */
    private $storage;

    function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function addTrans($parameters)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'transactionId', 'total',
        ))->setDefaults(array(
            'affiliation' => '',
            'tax'         => '',
            'shipping'    => '',
            'city'        => '',
            'state'       => '',
            'country'     => '',
        ));

        $parameters = $resolver->resolve($parameters);

        $this->storage->track('_addTrans', $parameters);
    }

    public function addItem($parameters)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'sku', 'name', 'price', 'quantity',
        ))->setDefaults(array(
            'transactionId' => '',
            'category'      => '',
        ));

        $parameters = $resolver->resolve($parameters);

        $this->storage->track('_addItem', $parameters);
    }

    public function trackTrans()
    {
        $this->storage->track('_trackTrans', true);
    }

    public function render()
    {
        $return = '';

        // Render _addTrans method then clear the key in the storage
        foreach ($this->storage->get('_addTrans', array()) as $trans) {
            $return .= sprintf("_gaq.push(['_addTrans', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);", $trans['transactionId'], $trans['affiliation'], $trans['total'], $trans['tax'], $trans['shipping'], $trans['city'], $trans['state'], $trans['country']);
        }

        $this->storage->clear('_addTrans');

        // Render _addItem method then clear the key in the storage
        foreach ($this->storage->get('_addItem', array()) as $item) {
            $return .= sprintf("_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s' ]);", $item['transactionId'], $item['sku'], $item['name'], $item['category'], $item['price'], $item['quantity']);
        }

        $this->storage->clear('_addItem');

        // Render _trackTrans method then clear the key in the storage
        if ($this->storage->get('_trackTrans', false)) {
            $return .= "_gaq.push(['_trackTrans']);";
        }

        $this->storage->clear('_trackTrans');

        return $return;
    }

}
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
            'transactionId',
        ))->setDefaults(array(
            'affiliation' => null,
            'tax'         => null,
            'shipping'    => null,
            'revenue'     => null
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

        foreach ($this->storage->get('_addTrans', array()) as $transaction) {
            $return .= $this->renderAddTransaction($transaction);
        }
        $this->storage->clear('_addTrans');

        foreach ($this->storage->get('_addItem', array()) as $item) {
            $return .= $this->renderAddItem($item);
        }
        $this->storage->clear('_addItem');

        if ($this->storage->get('_trackTrans', false)) {
            $return .= $this->renderSendTransaction();
        }
        $this->storage->clear('_trackTrans');

        return $return;
    }

    public function renderAddTransaction(array $transaction)
    {
        return sprintf(
            'ga("ecommerce:addTransaction",%s);%s', 
            json_encode($this->getTransactionParameters($transaction)),
            PHP_EOL
        );
    }

    protected function getTransactionParameters(array $transaction)
    {
        $parameters = [
            'id' => $transaction['transactionId'],
        ];

        foreach([
            'affiliation' => 'affiliation',
            'revenue' => 'revenue',
            'tax' => 'tax',
            'shipping' => 'shipping',
        ] as $label => $gaLabel) {
            if (array_key_exists($label, $transaction)) {
                $parameters[$gaLabel] = $transaction[$label];
            }
        }

        return $parameters;
    }

    public function renderAddItem(array $item)
    {
        return sprintf(
            'ga("ecommerce:addItem",%s);%s', 
            json_encode($this->getItemParameters($item)),
            PHP_EOL
        );
    }

    protected function getItemParameters(array $item)
    {
        $parameters = [
            'id' => $item['transactionId'],
            'name' => $item['name'],
        ];

        foreach([
            'sku',
            'category',
            'price',
            'quantity',
        ] as $label) {
            if (array_key_exists($label, $item)) {
                $parameters[$label] = $item[$label];
            }
        }

        return $parameters;
    }

    public function renderSendTransaction()
    {
        return 'ga("ecommerce:send");'.PHP_EOL;
    }
}

<?php

namespace spec\Betacie\Google\Tracker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Betacie\Google\Storage\StorageInterface;

class EcommerceTrackerSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\Google\Tracker\EcommerceTracker');
        $this->shouldImplement('Betacie\Google\Tracker\TrackerInterface');
    }

    function it_renders_add_transaction()
    {
        $this->renderAddTransaction([
            'transactionId' => '1234',
            'affiliation' => 'FooAffiliation',
            'revenue' => 23.50,
            'tax' => 4.5,
            'shipping' => 10,
        ])->shouldReturn('ga("ecommerce:addTransaction",{"id":"1234","affiliation":"FooAffiliation","revenue":23.5,"tax":4.5,"shipping":10});'.PHP_EOL);
    }

    function it_renders_add_item()
    {
        $this->renderAddItem([
            'transactionId' => '1234',
            'sku' => 'TSHIRT',
            'name' => 'Hello Kitty',
            'category' => 'Clothing',
            'price' => 19.99,
            'quantity' => 2,
        ])->shouldReturn('ga("ecommerce:addItem",{"id":"1234","name":"Hello Kitty","sku":"TSHIRT","category":"Clothing","price":19.99,"quantity":2});'.PHP_EOL);
    }

    function it_renders_the_sent_transaction()
    {
        $this->renderSendTransaction()->shouldReturn('ga("ecommerce:send");'.PHP_EOL);
    }

    function it_renders_all_stored_calls($storage)
    {
        //add one transaction
        $storage->get('_addTrans', [])->willReturn([[
            'transactionId' => '1234',
            'revenue' => 23.50,
        ]]);
        $storage->clear('_addTrans')->shouldBeCalled();
        
        //add two items
        $storage->get('_addItem', [])->willReturn([
            [
                'transactionId' => '1234',
                'name' => 'Hello Kitty',
            ],
            [
                'transactionId' => '1234',
                'name' => 'Nirvana',
            ]
        ]);
        $storage->clear('_addItem')->shouldBeCalled();

        //send transaction
        $storage->get('_trackTrans', false)->willReturn(true);
        $storage->clear('_trackTrans')->shouldBeCalled();

        $this->render()->shouldReturn(
             'ga("ecommerce:addTransaction",{"id":"1234","revenue":23.5});'.PHP_EOL
            .'ga("ecommerce:addItem",{"id":"1234","name":"Hello Kitty"});'.PHP_EOL
            .'ga("ecommerce:addItem",{"id":"1234","name":"Nirvana"});'.PHP_EOL
            .'ga("ecommerce:send");'.PHP_EOL
        );
    }
}

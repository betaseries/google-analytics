<?php

namespace spec\Betacie\Google\Tracker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Betacie\Google\Storage\StorageInterface;
use Betacie\Google\Tracker\EventTracker;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class EventTrackerSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\Google\Tracker\EventTracker');
        $this->shouldImplement('Betacie\Google\Tracker\TrackerInterface');
    }

    function it_can_track_an_event($storage)
    {
        $this->shouldThrow(new MissingOptionsException('The required options "action", "category" are missing.'))->duringTrackEvent([]);
        
        $this->shouldThrow(new UndefinedOptionsException('The option "foo" does not exist. Defined options are: "action", "category", "label", "noninteraction", "value".'))->duringTrackEvent([
            'category' => 'Cat1',
            'action' => 'Action1',
            'foo' => 'bar'
        ]);

        $storage->track('_trackEvent', [
            'category' => 'MyCategory1',
            'action' => 'MyAction1',
            'label' => 'MyLabel1',
            'value' => 'Foobar1',
            'noninteraction' => true
        ])->shouldBeCalled();

        $this->trackEvent([
            'category' => 'MyCategory1',
            'action' => 'MyAction1',
            'label' => 'MyLabel1',
            'value' => 'Foobar1',
            'noninteraction' => true
        ]);

        //minimum requirements
        $storage->track('_trackEvent', [
            'category' => 'MyCategory1',
            'action' => 'MyAction1',
            'label' => null,
            'value' => null,
            'noninteraction' => null
        ])->shouldBeCalled();

        $this->trackEvent([
            'category' => 'MyCategory1',
            'action' => 'MyAction1',
        ]);
    }

    function it_renders_all_events($storage)
    {
        $events = [
            [
                'category' => 'MyCategory1',
                'action' => 'MyAction1',
                'label' => 'MyLabel1',
                'value' => 'Foobar1',
                'noninteraction' => true
            ],
            [
                'category' => 'MyCategory2',
                'action' => 'MyAction2',
                'label' => 'MyLabel2',
                'value' => 'Foobar2',
                'noninteraction' => false
            ]
        ];

        $storage->get('_trackEvent', [])->willReturn($events);

        $storage->clear('_trackEvent')->shouldBeCalled();

        $gaEvents  = 'ga("send","event","MyCategory1","MyAction1",{"eventLabel":"MyLabel1","eventValue":"Foobar1","nonInteraction":true});'.PHP_EOL;
        $gaEvents .= 'ga("send","event","MyCategory2","MyAction2",{"eventLabel":"MyLabel2","eventValue":"Foobar2","nonInteraction":false});'.PHP_EOL;

        $this->render()->shouldReturn($gaEvents);
    }

    function it_renders_an_event()
    {
        $this->renderEvent([
            'category' => 'MyCategory',
            'action' => 'MyAction',
            'label' => null,
            'value' => null,
            'noninteraction' => null
        ])->shouldReturn(
            'ga("send","event","MyCategory","MyAction",[]);'.PHP_EOL
        );
    }
}

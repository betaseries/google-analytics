<?php

namespace spec\Betacie\Google;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Betacie\Google\Tracker\TrackerInterface;

class AnalyticsSpec extends ObjectBehavior
{
    function let(TrackerInterface $tracker)
    {
        $this->beConstructedWith('UA-XXXX-Y', $tracker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\Google\Analytics');
    }

    function it_checks_if_tracking_id_is_set($tracker)
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('Parameter "%betacie_google.analytics.tracking_id%" has not been set.'))
            ->during('__construct', [null, $tracker])
        ;
    }

    function it_has_a_tracker($tracker)
    {
        $this->getTracker()->shouldReturn($tracker);
    }

    function it_renders_the_entire_google_analytics($tracker)
    {
        $tracker->render()->willReturn('ga("somecalls");'.PHP_EOL);

        $this->render()->shouldReturn(
             '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,"script","//www.google-analytics.com/analytics.js","ga");'.PHP_EOL
            .'ga("create", "UA-XXXX-Y", "auto");'.PHP_EOL
            .'ga("send", "pageview");'.PHP_EOL
            .'ga("somecalls");'.PHP_EOL
        );
    }

    function it_renders_the_global_object()
    {
        $this->renderGlobalObject()->shouldReturn(
            '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,"script","//www.google-analytics.com/analytics.js","ga");'.PHP_EOL
        );
    }

    function it_renders_the_create_account()
    {
        $this->renderCreateAccount()->shouldReturn('ga("create", "UA-XXXX-Y", "auto");'.PHP_EOL);
    }

    function it_renders_a_page_view()
    {
        $this->renderPageView()->shouldReturn('ga("send", "pageview");'.PHP_EOL);
    }
}

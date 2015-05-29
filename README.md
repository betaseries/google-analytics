# google-analytics
Tracking events and ecommerce with google analytics

```php
<?php

use Betacie\Google\Tracker\EventTracker;
use Betacie\Google\Storage\ArrayStorage;

$storage = new ArrayStorage();
$tracker = new EventTracker($storage);

// You can track many event thanks to Storage classes, you can choose ArrayStorage or SessionStorage
// SessionStorage will be persistant until you render your tracking code.
$tracker->trackEvent([
    'category' => 'Registration',
    'action' => 'Confirmed',
    'label' => 'user-1',
]);

$tracker->trackEvent([
    'category' => 'Registration',
    'action' => 'Completed',
    'label' => 'user-1',
]);

// render() will print the tracking code and clear all event already store, this prevent duplicate tracking
echo $tracker->render();
```

### Usage with Symfony
You could defined Storage and Tracker as Symfony service and inject them in other services.

```xml
<!-- Tracker -->
<service id="google.event_tracker" class="Betacie\Google\Tracker\EventTracker">
    <argument type="service" id="betacie_google.storage" />
</service>

<!-- Session -->
<service id="google.tracking_bag" class="Betacie\Google\Session\TrackingBag" />

<!-- Storage -->
<service id="google.session_storage" class="Betacie\Google\Storage\SessionStorage">
    <argument type="service" id="session" />
    <argument type="service" id="betacie_google.tracking_bag" />
</service>
```


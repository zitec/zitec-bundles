<?php

namespace Zitec\JSDataBundle\Event;

/**
 * Defines the events managed by the bundle.
 */
final class Events
{
    /**
     * When data is demanded for the first time from the data handler service, an event is dispatched in order to
     * let other components alter the final data set.
     */
    const DATA_COLLECT = 'zitec.js_data.data_collect';
}

<?php

declare(strict_types=1);

namespace Zitec\JSDataBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Zitec\JSDataBundle\DataCollector\DataCollectorInterface;

/**
 * Represents an event which is triggered when data is demanded for the first time from the data handler
 * service. Other bundles can listen to it in order to alter the JS data set.
 */
class DataCollectEvent extends Event
{
    protected DataCollectorInterface $dataCollector;

    public function __construct(DataCollectorInterface $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }

    public function getDataCollector(): DataCollectorInterface
    {
        return $this->dataCollector;
    }
}

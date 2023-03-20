<?php

namespace Zitec\JSDataBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Zitec\JSDataBundle\DataCollector\DataCollectorInterface;

/**
 * Represents an event which is triggered when data is demanded for the first time from the data handler
 * service. Other bundles can listen to it in order to alter the JS data set.
 */
class DataCollectEvent extends Event
{
    /**
     * The data collector.
     *
     * @var DataCollectorInterface
     */
    protected DataCollectorInterface $dataCollector;

    /**
     * The event constructor.
     *
     * @param DataCollectorInterface $dataCollector
     */
    public function __construct(DataCollectorInterface $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }

    /**
     * Data collector getter.
     *
     * @return DataCollectorInterface
     */
    public function getDataCollector(): DataCollectorInterface
    {
        return $this->dataCollector;
    }
}

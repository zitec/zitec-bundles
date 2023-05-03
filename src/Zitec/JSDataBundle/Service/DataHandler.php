<?php

declare(strict_types=1);

namespace Zitec\JSDataBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zitec\JSDataBundle\DataCollector\DataCollectorInterface;
use Zitec\JSDataBundle\Event\DataCollectEvent;

/**
 * A service which handles the data sent to the client's scripts. Bundles throughout the application may use
 * this service to add their own or alter the information from the JS data set.
 */
class DataHandler
{
    /**
     * The data collector used to collect JS data.
     */
    protected DataCollectorInterface $dataCollector;

    /**
     * The event dispatcher service.
     */
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * Flag which marks if data was collected from other bundles.
     */
    protected bool $collected = false;

    public function __construct(DataCollectorInterface $dataCollector, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataCollector = $dataCollector;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Adds the given value into the data collector.
     */
    public function add(string $path, mixed $value): DataHandler
    {
        $this->dataCollector->add($path, $value);

        return $this;
    }

    /**
     * Merges the given data set into the collector.
     */
    public function merge(array $data): DataHandler
    {
        $this->dataCollector->merge($data);

        return $this;
    }

    /**
     * Collects data from other bundles by dispatching the data_collect event.
     */
    protected function collect(): void
    {
        // The event is triggered only once.
        if ($this->collected) {
            return;
        }

        // Dispatch the data_collect event, in order to let other bundles alter the final data set.
        $this->eventDispatcher->dispatch(new DataCollectEvent($this->dataCollector));

        $this->collected = true;
    }

    /**
     * Fetches the collected data.
     */
    public function getAll(): array
    {
        $this->collect();

        return $this->dataCollector->getAll();
    }
}

<?php

namespace Zitec\SettingsBundle\Event;

use Zitec\SettingsBundle\DataCollector\DataCollectorInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The event is dispatched when we want to gather all settings objects
 */
class SettingsGenerateEvent extends Event
{
    const NAME = 'settings.generate';

    /**
     * The data collector.
     *
     * @var DataCollectorInterface
     */
    protected $dataCollector;

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
    public function getDataCollector()
    {
        return $this->dataCollector;
    }
}
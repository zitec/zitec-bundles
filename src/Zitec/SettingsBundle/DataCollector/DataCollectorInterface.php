<?php

namespace Zitec\SettingsBundle\DataCollector;

/**
 * Represents an interface which all data collectors must implement.
 * A data collector is nothing more than an object whose role is to collect data from different components
 */
interface DataCollectorInterface
{
    /**
     * Sets a value at the given path in the data object.
     *
     * @param mixed $value
     *
     * @return DataCollectorInterface
     */
    public function add($value);

    /**
     * Fetches the collected data.
     *
     * @return array
     */
    public function getAll();
}

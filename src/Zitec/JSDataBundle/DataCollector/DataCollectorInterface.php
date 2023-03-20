<?php

namespace Zitec\JSDataBundle\DataCollector;

/**
 * Represents an interface which all JS data collectors must implement. A data collector is nothing more than an object
 * whose role is to collect data from different components. At a given point, the collected data will be fetched
 * in a template and assigned to a JS variable. Basically, the data collector transports pieces of information
 * from PHP to JS.
 */
interface DataCollectorInterface
{
    /**
     * Sets a value at the given path in the data object.
     *
     * @param string $path
     * @param mixed $value
     *
     * @return DataCollectorInterface
     */
    public function add(string $path, $value): DataCollectorInterface;

    /**
     * Merges the given data set to the existing set.
     *
     * @param array $data
     *
     * @return DataCollectorInterface
     */
    public function merge(array $data): DataCollectorInterface;

    /**
     * Fetches the collected data.
     *
     * @return array
     */
    public function getAll(): array;
}

<?php

declare(strict_types=1);

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
     */
    public function add(string $path, mixed $value): DataCollectorInterface;

    /**
     * Merges the given data set to the existing set.
     */
    public function merge(array $data): DataCollectorInterface;

    /**
     * Fetches the collected data.
     */
    public function getAll(): array;
}

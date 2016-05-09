<?php

namespace Zitec\JSDataBundle\DataCollector;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Zitec\JSDataBundle\Utils\Common;

/**
 * The default data collector implementation. It uses the PropertyAccess core Symfony component to save values
 * in the data collection.
 */
class DefaultDataCollector implements DataCollectorInterface
{
    /**
     * The collected data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Flag which marks if the collected data was unloaded. This happens when the getAll method is called for the
     * first time. After unloading, the add and merge methods cannot be called anymore and doing this will cause
     * an exception to be thrown.
     *
     * @var bool
     */
    protected $unloaded = false;

    /**
     * The property accessor. It will be used to save values in the data collection.
     *
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * The data collector constructor.
     */
    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Checks if the collector was unloaded and if so, throws an exception.
     *
     * @throw \RuntimeException
     */
    protected function checkIfUnloaded()
    {
        if ($this->unloaded) {
            throw new \RuntimeException('The collector was already unloaded! You cannot load data into it anymore!');
        }
    }

    /**
     * {@inheritdoc}
     *
     * Check the documentation of the {@see PropertyAccessor::setValue()} method on how to set a value into a
     * nested array structure (which the data collection is).
     */
    public function add($path, $value)
    {
        $this->checkIfUnloaded();
        $this->accessor->setValue($this->data, $path, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $data)
    {
        $this->checkIfUnloaded();
        $this->data = Common::mergeArraysRecursive($this->data, $data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        $this->unloaded = true;

        return $this->data;
    }
}

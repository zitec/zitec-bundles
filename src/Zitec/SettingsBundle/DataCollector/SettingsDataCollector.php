<?php

namespace Zitec\SettingsBundle\DataCollector;

use Zitec\SettingsBundle\Entity\Settings;

/**
 * The default data collector implementation.
 */
class SettingsDataCollector implements DataCollectorInterface
{
    /**
     * The collected data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * {@inheritdoc}
     *
     */
    public function add($value)
    {
        if (!($value instanceof Settings)) {
            throw new \InvalidArgumentException('The supplied value must be an instance of the Settings class');

        }

        // add the value to the data list
        $this->data[$value->getCode()] = $value;

    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->data;
    }
}

<?php

namespace Zitec\SettingsBundle\Entity;

class Settings
{
    /**
     * The auto-generated ID of the setting
     *
     * @var integer
     */
    private $id;

    /**
     * The code of the setting (also used as unique identifier)
     *
     * @var string
     */
    private $code;

    /**
     * The name of the setting
     *
     * @var string
     */
    private $name;

    /**
     * Value for the setting
     *
     * @var string
     */
    private $value;

    /**
     * Optional description for the setting
     *
     * @var string
     */
    private $description;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the code for the parameter
     * This is used to uniquely identify a parameter
     *
     * @param string $code
     *
     * @return Settings
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the code for the parameter
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value
     *
     * @param string $value
     *
     * @return Settings
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the name of the parameter
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the parameter
     *
     * @param string $name
     *
     * @return Settings
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the description for the setting
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description for the setting
     *
     * @param string $description
     *
     * @return Settings
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }


    /**
     * Return the name of the setting when doing toString
     *
     * @return string
     */
    public function __toString()
    {
        if (!empty($this->name)) {
            return $this->name;
        }

        return '-';
    }
}


<?php

namespace Zitec\SettingsBundle\Services;

use Doctrine\ORM\EntityManager;

class Settings
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Settings constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Return the parameter value for a parameter name
     *
     * @param $name
     *
     * @return null|string
     */
    public function get($name)
    {
        if (!empty($name)) {
            $entry = $this->entityManager->getRepository('SettingsBundle:Settings')->findOneBy(array('code' => $name));
            if ($entry) {
                return $entry->getValue();
            }
        }

        return null;
    }
}


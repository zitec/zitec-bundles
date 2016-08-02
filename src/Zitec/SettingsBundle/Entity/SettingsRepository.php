<?php

namespace Zitec\SettingsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SettingsRepository extends EntityRepository
{
    /**
     * Fetches all the Settings entities indexed by the given property's values.
     *
     * @param string $indexBy
     *
     * @return Settings[]
     */
    public function findAllIndexed($indexBy)
    {
        return $this->createQueryBuilder('s', "s.$indexBy")->getQuery()->getResult();
    }
}

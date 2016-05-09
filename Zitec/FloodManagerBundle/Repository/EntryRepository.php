<?php

namespace Zitec\FloodManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * The flood entry entity custom repository.
 */
class EntryRepository extends EntityRepository
{
    /**
     * Fetches the number of times an event was executed by a given source after a given time.
     * 
     * @param string $event
     * @param string $source
     * @param \DateTime $after
     * 
     * @return int
     */
    public function getNumberOfAttempts($event, $source, \DateTime $after)
    {
        return $this->createQueryBuilder('e')
            ->select('count(e)')
            ->where('e.event = :event AND e.source = :source AND e.created >= :after')
            ->setParameter('event', $event)
            ->setParameter('source', $source)
            ->setParameter('after', $after)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

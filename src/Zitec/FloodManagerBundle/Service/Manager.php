<?php

namespace Zitec\FloodManagerBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Log\LoggerInterface;
use Zitec\FloodManagerBundle\Entity\Entry;

/**
 * The flood manager service. It is used to determine flood attempts.
 * For usage, consider you have certain events in your application (e.g. user login, user registration) and
 * these events are caused by sources (e.g. the user, which is determined mainly by IP address).
 * Every time a watched event is caused, you should register an entry for the current source (with the addEntry
 * method). If that event is caused by more than a defined number of times by a single source, then the service
 * will consider this to be a flood attempt (the isEventAllowed method).
 */
class Manager
{
    /**
     * The Doctrine service.
     *
     * @var Registry
     */
    protected $doctrine;

    /**
     * The logger service.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Flag which determines if the flood attempts should be logged or not.
     *
     * @var boolean
     */
    protected $logFloodAttempts;

    /**
     * The class constructor.
     *
     * @param Registry $doctrine
     * @param LoggerInterface $logger
     * @param boolean $logFloodAttempts
     */
    public function __construct(Registry $doctrine, LoggerInterface $logger, $logFloodAttempts = true)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->logFloodAttempts = $logFloodAttempts;
    }

    /**
     * Adds/subtracts a time interval to/from a given DateTime object.
     *
     * @param \DateTime $time
     * @param int $interval
     * - the time interval: may be represented as a number of seconds or as a string compatible with the
     * strtotime function;
     * @param boolean $after
     * - determines if the interval should be added or subtracted;
     *
     * @throws \InvalidArgumentException
     */
    protected function alterTime(\DateTime $time, $interval, $after = true)
    {
        $sign = $after ? '+' : '-';
        if (is_int($interval) && $interval > 0) {
            $time->modify("$sign $interval seconds");
        } elseif (is_string($interval)) {
            $time->modify("$sign $interval");
        } else {
            throw new \InvalidArgumentException('The interval must be either a string or a positive integer!');
        }
    }

    /**
     * Registers an entry for the given event and source.
     *
     * @param string $event
     * - the unique identifier of the event;
     * @param string $source
     * - the identifier of the source;
     * @param int|string $window
     * - the time interval after which this entry expires. This value should be the same with the one used
     * to interrogate the isEventAllowed method. It is used for deleting entries which aren't considered
     * anymore to prevent the indefinite growing of the entries table;
     */
    public function addEntry($event, $source, $window = 3600)
    {
        $created = new \DateTime();

        $expires = new \DateTime();
        $this->alterTime($expires, $window);

        $entry = new Entry();
        $entry->setEvent($event)
            ->setSource($source)
            ->setCreated($created)
            ->setExpires($expires);

        $manager = $this->doctrine->getManager();
        $manager->persist($entry);
        $manager->flush();
    }

    /**
     * Determines if a source still has access to a given action.
     *
     * @param string $event
     * - the unique identifier of the event (e.g. user login);
     * @param string $source
     * - the identifier of the source (e.g. IP address, hostname);
     * @param int $threshold
     * - the number of times a source may perform the corresponding action in a interval of time
     * (represented by $window);
     * @param int|string $window
     * - described above. It may be an integer representing the length in seconds of the interval or a string
     * describing the interval, readable by the strtotime function;
     *
     * @return boolean
     */
    public function isEventAllowed($event, $source, $threshold, $window = 3600)
    {
        $after = new \DateTime();
        $this->alterTime($after, $window, false);

        $nrOfAttempts = $this->doctrine->getManager()
            ->getRepository('Zitec\FloodManagerBundle\Entity\Entry')
            ->getNumberOfAttempts($event, $source, $after);

        $allowed = ($nrOfAttempts < $threshold);
        if (!$allowed && $this->logFloodAttempts) {
            $this->logger->warning(
                'ZitecFloodManagerBundle: Flood attempt detected from source {source} for event {event}!',
                array('source' => $source, 'event' => $event)
            );
        }

        return $allowed;
    }
}

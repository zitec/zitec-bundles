<?php

namespace Zitec\SettingsBundle\Services;

use Zitec\SettingsBundle\DataCollector\SettingsDataCollector;
use Zitec\SettingsBundle\Entity\Settings as SettingsEntity;
use Zitec\SettingsBundle\Event\SettingsGenerateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Service which handles the generation of SEO config entities by scanning the routing configuration.
 */
class SettingsConfigGenerator
{
    /**
     * The doctrine service.
     *
     * @var Registry
     */
    protected $doctrine;

    /**
     * The routing service.
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     * SettingsConfigGenerator constructor.
     *
     * @param Registry $doctrine
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Registry $doctrine, EventDispatcherInterface $eventDispatcher)
    {
        $this->doctrine = $doctrine;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataCollector = new SettingsDataCollector();
    }

    /**
     * Generate the application settings by triggering an event
     * to gather data from all bundles:
     *  - merge existing ones (without value override)
     *  - save new ones
     *  - discard obsolete settings
     */
    public function generate()
    {
        // dispatch the event and get all the attached items
        $event = new SettingsGenerateEvent($this->dataCollector);
        $this->eventDispatcher->dispatch(SettingsGenerateEvent::NAME, $event);
        /**
         * @var $settings SettingsEntity[]
         */
        $settings = $event->getDataCollector()->getAll();

        // get the already stored entities.
        $entities = $this->doctrine->getRepository('SettingsBundle:Settings')->findAllIndexed('code');
        $manager = $this->doctrine->getManager();

        foreach ($settings as $setting) {
            if (!isset($entities[$setting->getCode()])) {
                $manager->persist($setting);
            } else {
                // Update the existing entries.
                $entities[$setting->getCode()]
                    ->setName($setting->getName())
                    ->setDescription($setting->getDescription());
            }
        }

        // delete the entities which represent routes which don't exist anymore.
        $orphanEntities = array_diff_key($entities, $settings);
        foreach ($orphanEntities as $orphanEntity) {
            $manager->remove($orphanEntity);
        }

        // commit the changes.
        $manager->flush();
    }
}

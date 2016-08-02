<?php
namespace Zitec\SettingsBundle\EventSubscriber;

use Zitec\SettingsBundle\Entity\Settings;
use Zitec\SettingsBundle\Event\SettingsGenerateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SettingsGenerateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            SettingsGenerateEvent::NAME => 'onSettingsGenerate'
        );
    }

    /**
     * Add the settings through the data collector
     *
     * @param SettingsGenerateEvent $event
     */
    public function onSettingsGenerate(SettingsGenerateEvent $event)
    {
        $settingsDataCollector = $event->getDataCollector();

        $generalTrackingCode = new Settings();
        $generalTrackingCode->setName('Application tracking codes');
        $generalTrackingCode->setCode('application.tracking_codes');
        $generalTrackingCode->setDescription('You can add as many script tags as possible. These will be rendered before the </body> tag.');
        $settingsDataCollector->add($generalTrackingCode);

        $settingsDataCollector->add($generalTrackingCode);
    }
}
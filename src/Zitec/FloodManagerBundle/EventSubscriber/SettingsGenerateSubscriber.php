<?php

namespace Zitec\FloodManagerBundle\EventSubscriber;

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

    public function onSettingsGenerate(SettingsGenerateEvent $event)
    {
        $settingsDataCollector = $event->getDataCollector();

        $defaultAttempts = new Settings();
        $defaultAttempts->setName('Flood manager default attempts');
        $defaultAttempts->setCode('flood_manager.default_attempts');
        $defaultAttempts->setDescription('The number of attempts the user can perform in the specified time.');
        $settingsDataCollector->add($defaultAttempts);

        $defaultTime = new Settings();
        $defaultTime->setName('Flood manager default time interval');
        $defaultTime->setCode('flood_manager.default_time_interval');
        $defaultTime->setDescription('The time interval(in MINUTES) in which the user can submit the forms.');
        $settingsDataCollector->add($defaultTime);
    }
}
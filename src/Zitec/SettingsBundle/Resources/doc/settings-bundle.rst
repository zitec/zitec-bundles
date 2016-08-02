SettingsBundle
    This package creates entity setting of the application and collects

    Main components:
        •	DataCollectorInterface:
            o	Represents an interface which all data collectors must implement.A data collector is nothing more than an object whose role is to
            collect data from different components.
            o	Functions:
                	add($value): sets a value at the given path in the data object;
                	getAll(): fetches the collected data.

        •	SettingsDataCollector:
            o	The default data collector implementation. Implements DataCollectorInterface.
            o	Components:
                	$data = array(): the collected data.
            o	Functions:
                	add($value): add setting in $data;
                	getAll(): get all collected data;

        •	Settings:
            o	Setting entity.
            o	Components:
                	$id: the auto-generated ID of the setting;
                	$code: the of the setting ( also used as unique identifier);
                	$name: the of the setting;
                	$value: value for the setting;
                	$description: optional description for the setting;

        •	SettingsGenerateEvent:
            o	The event is dispatched when we want to gather all settings objects.
            o	Components:
                	$dataCollector: the data collector.
            o	Functions:
                	getDataCollector(): get data collector interface.

        •	Settings:
            o	A service which return value for a setting.
            o	Functions:
                	get($name): return the parameter value for a parameter name.

        •	SettingsConfigGenerator:
            o	Service which handles the generation of SEO config entities by scanning the routing configuration.
            o	Components:
                	$doctrine;
                	$router;
            o	Functions:
                	generate(): Generate the application settings by triggering an event to gather data from all bundles: merge existing ones(without value override),
                save new ones and discard obsolete settings.

    Usage:
    1.	Create an event subscriber which subscribed an settings generate event.
        Example:
            -	Create application tracking codes

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

    2. Go to admin and generate settings;
    3. Set value for current settings;

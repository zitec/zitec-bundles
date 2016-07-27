JSDataBundle

    This bundle transports pieces of information from PHP to JS.

    Main components:
         • DataCollectorInterface
            o Represents an interface which all JS data collectors must implement. A data collector is nothing more than an object whose role is to collect data
              from different components. At a given point, the collected data will be fetched in a template and assigned to a JS variable. Basically, the data
              collector transports pieces of information from PHP to JS.
            o Functions:
                 getAll(): Fetches the collected data.
                 add($path, $value):  Sets a value at the given path in the data object.
                 merge(array $data): Merges the given data set to the existing set.

         • DataCollectEvent:
            o Represents an event which is triggered when data is demanded for the first time from the data handler service. Other bundles can listen to it in
              order to alter the JS data set.

         • DataHandler:
            o A service which handles the data sent to the client's scripts. Bundles throughout the application may use this service to add their own or
              alter the information from the JS data set.
            o Properties:
                 dataCollector: the data collector used to collect JS data.
                 eventDispatcher: the event dispatcher service.
                 collected (bool): flag which marks if data was collected from other bundles.
            o	Functions:
                 add($path, $value): adds the given value into the data collector.
                 merge(array data): merges the given data set into the collector.
                 getAll(): fetches the collected data.
                 collect(): collects data from other bundles by dispatching the data_collect event.  The event is triggered only once.

         •	JSDataExtension:
            o Twig extension which makes the JS data handler available in the templates through functions which are proxies to its methods.
            o Properties:
                 $dataHandler: the data handler service.
            o Functions:
                 getFunctions()
                    • zitec_js_data_add call addFunction;
                    • zitec_js_data_merge call mergeFunction;
                    • zitec_js_data_get_all call getAllFunction;
                 getName(): return ‘zitec_js_data_extensions’;
                 addFunction($path, $value): call $dataHandler->add($path, $value);
                 mergeFunction(array $data): call $dataHandler->merge($data);
                 getAllFunction($jsonEncodeOptions):
            • $jsonEncodeOptions: default value is 0;
            • The data is outputted directly in the JSON format,so you can assign it to a JS variable.

    Example:

        Add parameter in jsDataHandler:
                $jsDataHandler = $this->get('zitec.js_data.data_handler');
                $categories = array('Test1', 'Test2');
                $jsDataHandler->add('[Category]',$categories);

        Get all parameters in TWIG:
             {{ zitec_js_data_get_all() }}




            FormAutocompleteBundle

    Components:

    •	AutocompleteController:
        o	Controller which handles autocomplete specific actions.
        o	Functions:
            	indexAction(Request $request, $dataResolverId) : internal action which provides autocomplete suggestions specific to the given data resolver;
            	parametersIsValid($parameter): validate that the parameter we are receiving have the proper data types.The parameter must be a scalar value or empty.

    •	DataResolverInterface:
        o	A data resolver is an object which manages the data of an autocomplete field. When a programmer attaches an autocomplete field to a form, it must
        also specify a data resolver for it. It should be able to:
            	suggest items matching the user's search criteria;
            	transform the user input into application data;
            	transform the application data into view data (the reverse of the preceding operation);
        o	Functions:
            	getSuggestions($term, $context = null): Given the user search term, returns a list of matching suggestions.
                •	@param: string $term
                •	@param: mixed $context: when demanding autocomplete suggestions, the client may also specify a context which can influence the result generation;
                •	@return: array - a set of arrays or objects (which can be JSON-serialized) with the following keys:
                        o	id: the identifier of the suggested item;
                        o	text: the label of the suggested item;
            	getModelData($viewData, $viewDataAlwaysString = false): Extracts the model data (the data used in the application) from the view data.
                •	@param mixed $viewData
                •	@param bool $viewDataAlwaysString: flag which specifies that the data received from the client will always be represented as a string,
                event if the field carries multiple values.
                •	@return mixed
            	getViewData($modelData): Extracts the view data (that will be used in the views) from the model data.
                •	@param: mixed $modelData
                •	@return: mixed – the data in the view should be represented as an array or a collection of arrays with the following keys:
                    o	Value: the actual data;
                    o	Label: a description of data;

    •	DataResolverManager:
        o	Manages the autocomplete data resolvers declared throughout the application.
        o	Fields:
            	$dataResolver: the collection of managed data resolvers keyed by their identifiers.
        o	Functions:
            	Get($key): Fetches the data resolver with the given key.

    •	EntityBaseDataResolver:
        o	Fields:
            	$doctrine: doctrine service;
            	$entityClass: the associated entity’s class;
            	$idPath: the path to the id property of the entity;
            	$labelPath: the path to the entity property which represents its label;
            	$suggestionsFetcher: the consumer may provide a custom function for fetching the suggestions data. The function will receive the term
            and should return an array of matching entities of the specified type. It will be represented in one of the forms:
                •	a simple string: denotes the name of a method from the entity repository;
                •	a callable: denotes the complete path to a function;
            	$propertyAccesor: a property accessor instance used for fetching the data from the entity;
        o	Functions:
            	callSuggestionsFetcher($term): calls the custom suggestions fetcher and return the result.
            	getSuggestionsData($term): fetches the suggestions raw data.
            	getSuggestions(): given the user search item, returns a list of the matching suggestions.

    •	EntitySingleDataResolver:
        o	Data resolver which relates the data of a single-value autocomplete field to an entity. Programmers may use directly this class in order to declare their data-resolver services.
        o	Functions:
            	getModelData($viewData, $viewDataAlwaysString): Extracts the model data (the data used in the application) from the view data.
            	getViewData($modelData): Extracts the view data (that will be used in the views) from the model data.

    •	EntitySingleDataResolver:
        o	Data resolver which relates the data of a multiple-value autocomplete field to an entity. Programmers may use directly this class in order to declare their data-resolver services.
        o	Functions:
            	getModelData($viewData, $viewDataAlwaysString): Extracts the model data (the data used in the application) from the view data.
            	getViewData($modelData): Extracts the view data (that will be used in the views) from the model data.

    •	DataResolverLoaderCompilerPass:
        o	Compiler pass which has the responsibility of registering all the data resolvers declared in the container into the data resolver manager. In order to declare a data resolver,
        the user must create a service that implements the DataResolverInterface, tag it and set an attribute on the tag which specifies the data resolver key.
        o	Fields:
            	DATA_RESOLVER_TAG: zitec_autocomplete_data_resolver;
            	DATA_RESOLVER_MANAGER_ID:zitec.form_autocomplete.data_resolver_manager;

    •	AutocompleteDataTransformer:
        o	The data transformer specific to the autocomplete form field type. It will use the data resolver specific to the currently handled field. Implements DataTransformerInterface.
        o	Fields:
            	$dataResolver: An autocomplete data resolver instance which will perform the data transformations;
            	$viewDataAlwaysString: Flag which marks if the data from the view will always be represented as a string (even when the field carries multiple values).
            The information will be propagated to the data resolver in order to format the view data accordingly.

    •	AutocompleteType:
        o	Defines the zitec autocomplete form field type. This field will be basically a text box with suggestions generated from the user input.
        o	Fields:
            	DEFAULT_AUTOCOMPLETE_PATH: zitec_form_autocomplete_autocomplete;
            	$router: the routing service;
            	$dataResolverManager: the data resolver manager service;

    Instalation:
        1. Add AutocompleteBundle in AppKernel.php;
        2. Add routing for autocmplete:
            zitec_form_autocomplete:
                resource: "@ZitecFormAutocompleteBundle/Resources/config/routing.yml"
                prefix:   /
        3. Add template fields in config.yml:
                twig:
                    form:
                        resources:
                            - 'ZitecFormAutocompleteBundle:Form:fields.html.twig'

        4. add js and css in template
            -  select2 library
            -  bundles/zitecformautocomplete/css/autocomplete.css
            - @ZitecFormAutocompleteBundle/Resources/public/js/autocomplete.js
            - @ZitecFormAutocompleteBundle/Resources/public/js/autocomplete_init.js

    Example:

        1. declare service used for handling the data of city autocomplete fields
            campaigns.form.autocomplete.data_resolver_cities_with_campaigns:
                class: Zitec\FormAutocompleteBundle\DataResolver\EntitySingleDataResolver
                arguments:
                    - @doctrine
                    - GeolocationsBundle\Entity\City
                    - id
                    - name
                    - getCityWithNameLike
                tags:
                    - { name: zitec_autocomplete_data_resolver, key: cities_with_campaigns_single }

        2. in city repository create function getCityWithNameLike
            public function getCityWithNameLike($cityName)
                {
                    $queryBuilder = $this->createQueryBuilder('c')
                            ->where('c.name like :name or c.internationalName like :name')
                            ->orderBy('c.name', 'ASC')
                            ->setParameter('name', '%'.$cityName.'%');
                    //fetch matching cities
                    $cities = $queryBuilder->getQuery()->getResult();
                    return $cities;
                }

        3. in form create autocomplete field
            ->add('city', 'zitec_autocomplete', array(
                'data_resolver' => 'cities_with_campaigns_single',
                'placeholder' => 'placeholder_campaign_list_city',
                'required' => false,
                'delay' => 250,
                'allow_clear' => true,
            ))





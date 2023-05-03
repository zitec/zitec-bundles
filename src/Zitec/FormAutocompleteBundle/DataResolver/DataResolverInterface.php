<?php

declare(strict_types=1);

namespace Zitec\FormAutocompleteBundle\DataResolver;

/**
 * A data resolver is an object which manages the data of an autocomplete field. It should be able to:
 *  - suggest items matching the user's search criteria;
 *  - transform the user input into application data;
 *  - transform the application data into view data (the reverse of the preceding operation);
 * When a programmer attaches an autocomplete field to a form, it must also specify a data resolver for it.
 */
interface DataResolverInterface
{
    /**
     * Given the user search term, returns a list of matching suggestions.
     *
     * @param string $term
     * @param mixed|null $context
     * - when demanding autocomplete suggestions, the client may also specify a context which can influence
     *   the result generation;
     *
     * @return array
     * - a set of arrays or objects (which can be JSON-serialized) with the following keys:
     *      - id: the identifier of the suggested item;
     *      - text: the label of the suggested item;
     */
    public function getSuggestions(string $term, mixed $context = null): array;

    /**
     * Extracts the model data (the data used in the application) from the view data.
     *
     * @param mixed $viewData
     * @param bool $viewDataAlwaysString
     * - flag which specifies that the data received from the client will always be represented as a string,
     *   event if the field carries multiple values;
     *
     * @return mixed
     */
    public function getModelData(mixed $viewData, bool $viewDataAlwaysString = false): mixed;

    /**
     * Extracts the view data (that will be used in the views) from the model data.
     *
     * @param mixed $modelData
     *
     * @return mixed
     * - the data in the view should be represented as an array or a collection of arrays with the following keys:
     *      - value: the actual data;
     *      - label: a description of the data;
     */
    public function getViewData(mixed $modelData): mixed;
}

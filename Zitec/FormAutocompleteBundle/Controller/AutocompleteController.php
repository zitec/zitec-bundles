<?php

namespace Zitec\FormAutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller which handles autocomplete specific actions.
 */
class AutocompleteController extends Controller
{
    /**
     * Internal action which provides autocomplete suggestions specific to the given data resolver.
     *
     * @param Request $request
     * @param string $dataResolverId
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request, $dataResolverId)
    {
        $term = $request->query->get('term');
        $context = $request->query->get('context');

        /**
         * I think it would be better if each service would define it's allowed data types
         */
        if ($this->parameterIsValid($term) && $this->parameterIsValid($context)) {
            $dataResolver = $this->get('zitec.form_autocomplete.data_resolver_manager')->get($dataResolverId);
            $suggestions = $dataResolver->getSuggestions($term, $context);
        } else {
            $suggestions = array();
        }

        return new JsonResponse(array('items' => $suggestions));
    }

    /**
     * Validate that the parameter we are receiving have the proper data types
     * The parameter must be a scalar value or empty
     *
     * @param $parameter
     *
     * @return bool
     */
    private function parameterIsValid($parameter)
    {
        if (is_scalar($parameter) || empty($parameter)) {
            return true;
        }
        return false;
    }
}

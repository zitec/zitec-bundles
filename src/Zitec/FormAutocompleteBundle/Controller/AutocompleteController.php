<?php

declare(strict_types=1);

namespace Zitec\FormAutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller which handles autocomplete specific actions.
 */
class AutocompleteController extends AbstractController
{
    /**
     * Internal action which provides autocomplete suggestions specific to the given data resolver.
     */
    public function indexAction(Request $request, string $dataResolverId): JsonResponse
    {
        $term = $request->query->get('term');
        $context = $request->query->get('context');

        /**
         * I think it would be better if each service would define it's allowed data types
         */
        if ($this->parameterIsValid($term) && $this->parameterIsValid($context)) {
            $dataResolver = $this->container->get('zitec.form_autocomplete.data_resolver_manager')->get($dataResolverId);
            $suggestions = $dataResolver->getSuggestions($term, $context);
        } else {
            $suggestions = [];
        }

        return new JsonResponse(['items' => $suggestions]);
    }

    /**
     * Validate that the parameter we are receiving have the proper data types
     * The parameter must be a scalar value or empty
     */
    private function parameterIsValid(mixed $parameter): bool
    {
        return is_scalar($parameter) || empty($parameter);
    }
}

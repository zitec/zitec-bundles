<?php

namespace Zitec\FormAutocompleteBundle\DataResolver;

/**
 * Defines a contract for data resolvers which limit their suggestions number.
 */
interface LimitAwareDataResolverInterface extends DataResolverInterface
{
    /**
     * The suggestions limit setter.
     *
     * @param int $suggestionsLimit
     *
     * @return $this
     */
    public function setSuggestionsLimit($suggestionsLimit);
}

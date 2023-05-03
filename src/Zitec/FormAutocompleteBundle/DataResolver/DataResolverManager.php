<?php

declare(strict_types=1);

namespace Zitec\FormAutocompleteBundle\DataResolver;

/**
 * Manages the autocomplete data resolvers declared throughout the application.
 */
class DataResolverManager
{
    /**
     * The collection of managed data resolvers keyed by their identifiers.
     *
     * @var DataResolverInterface[]
     */
    protected array $dataResolvers;

    public function __construct(array $dataResolvers)
    {
        $this->dataResolvers = $dataResolvers;
    }

    /**
     * Fetches the data resolver with the given key.
     *
     * @throws \DomainException
     */
    public function get(string $key): DataResolverInterface
    {
        // Check if the data resolver exists.
        if (array_key_exists($key, $this->dataResolvers)) {
            return $this->dataResolvers[$key];
        }

        // Handle the case when the data resolver isn't defined.
        throw new \DomainException(sprintf('The data resolver with the key "%s" wasn\'t found!', $key));
    }
}

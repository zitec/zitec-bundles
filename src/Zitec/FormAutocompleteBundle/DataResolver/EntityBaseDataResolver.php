<?php

namespace Zitec\FormAutocompleteBundle\DataResolver;

use LogicException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;

/**
 * Template for autocomplete data resolvers which relate the data from a field to an entity.
 */
abstract class EntityBaseDataResolver implements DataResolverInterface
{
    /**
     * The doctrine service.
     *
     * @var Registry
     */
    protected Registry $doctrine;

    /**
     * The associated entity's class.
     *
     * @var string
     */
    protected string $entityClass;

    /**
     * The path to the id property of the entity.
     *
     * @var string
     */
    protected string $idPath;

    /**
     * The path to the entity property which represents its label.
     *
     * @var string
     */
    protected string $labelPath;

    /**
     * The consumer may provide a custom function for fetching the suggestions data.
     *
     * @var string|callable|null
     * - the function will receive the term and should return an array of matching entities of the specified type.
     *   It will be represented in one of the forms:
     *      - a simple string: denotes the name of a method from the entity repository;
     *      - a callable: denotes the complete path to a function;
     */
    protected $suggestionsFetcher;

    /**
     * A property accessor instance used for fetching the data from the entity.
     *
     * @var PropertyAccessor
     */
    protected PropertyAccessor $propertyAccessor;

    /**
     * The data resolver constructor.
     *
     * @param Registry $doctrine
     * @param string $entityClass
     * @param string $idPath
     * @param string $labelPath
     * @param string|callable|null $suggestionsFetcher
     */
    public function __construct(
        Registry $doctrine,
        string $entityClass,
        string $idPath,
        string $labelPath,
        $suggestionsFetcher = null
    ) {
        $this->doctrine = $doctrine;
        $this->entityClass = $entityClass;
        $this->idPath = $idPath;
        $this->labelPath = $labelPath;
        $this->suggestionsFetcher = $suggestionsFetcher;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Calls the custom suggestions fetcher and returns the result.
     *
     * @param string $term
     * @return array
     *
     * @throws LogicException if the suggestions fetcher isn't well-defined
     */
    protected function callSuggestionsFetcher(string $term): array
    {
        /* @var $entityRepository EntityRepository */
        $entityRepository = $this->doctrine->getRepository($this->entityClass);

        if (is_string($this->suggestionsFetcher) && is_callable([$entityRepository, $this->suggestionsFetcher])) {
            return $entityRepository->{$this->suggestionsFetcher}($term);
        }

        if (is_callable($this->suggestionsFetcher)) {
            return call_user_func($this->suggestionsFetcher, $term);
        }

        throw new LogicException(
            'The suggestions fetcher may be either a string pointing to a repository method or a callable!'
        );
    }

    protected function getSuggestionsData(string $term): array
    {
        // Try to call the custom fetcher method if provided.
        if (null !== $this->suggestionsFetcher) {
            return $this->callSuggestionsFetcher($term);
        }

        // Build the default query. We will compare the entity labels with the term and fetch the matches.
        $classIndex = strrpos($this->entityClass, '\\');
        $entityAlias = (false === $classIndex) ? $this->entityClass : substr($this->entityClass, $classIndex + 1);
        $entityAlias = strtolower($entityAlias);

        return $this->doctrine
            ->getRepository($this->entityClass)
            ->createQueryBuilder($entityAlias)
            ->where("$entityAlias.$this->labelPath LIKE :term")
            ->setParameter(':term', "%$term%")
            ->getQuery()
            ->getResult();
    }

    public function getSuggestions(string $term, $context = null): array
    {
        // Fetch the suggestions raw data.
        $data = $this->getSuggestionsData($term);

        $suggestions = [];
        foreach ($data as $item) {
            $suggestions[] = [
                'id' => $this->propertyAccessor->getValue($item, $this->idPath),
                'text' => $this->propertyAccessor->getValue($item, $this->labelPath),
            ];
        }

        return $suggestions;
    }
}

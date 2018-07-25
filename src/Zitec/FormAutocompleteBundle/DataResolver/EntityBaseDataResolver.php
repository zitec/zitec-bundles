<?php

namespace Zitec\FormAutocompleteBundle\DataResolver;

use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;

/**
 * Template for autocomplete data resolvers which relate the data from a field to an entity.
 */
abstract class EntityBaseDataResolver implements LimitAwareDataResolverInterface
{
    /**
     * The doctrine service.
     *
     * @var Registry
     */
    protected $doctrine;

    /**
     * The associated entity's class.
     *
     * @var string
     */
    protected $entityClass;

    /**
     * The path to the id property of the entity.
     *
     * @var string
     */
    protected $idPath;

    /**
     * The path to the entity property which represents its label.
     *
     * @var string
     */
    protected $labelPath;

    /**
     * The consumer may provide a custom function for fetching the suggestions data.
     *
     * @var string|callable|null
     * - the function will receive the term and a limit and should return an array of matching entities of the specified
     *   type. It will be represented in one of the forms:
     *      - a simple string: denotes the name of a method from the entity repository;
     *      - a callable: denotes the complete path to a function;
     */
    protected $suggestionsFetcher;

    /**
     * A property accessor instance used for fetching the data from the entity.
     *
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * The maximum number of returned suggestions.
     *
     * @var int
     */
    protected $suggestionsLimit;

    /**
     * The entity manager to use when fetching data.
     *
     * @var string|null
     */
    protected $entityManagerName;

    /**
     * The data resolver constructor.
     *
     * @param Registry $doctrine
     * @param string $entityClass
     * @param string $idPath
     * @param string $labelPath
     * @param string|callable|null $suggestionsFetcher
     * @param string|null $entityManagerName
     */
    public function __construct(
        Registry $doctrine,
        $entityClass,
        $idPath,
        $labelPath,
        $suggestionsFetcher = null,
        $entityManagerName = null
    ) {
        $this->doctrine = $doctrine;
        $this->entityClass = $entityClass;
        $this->idPath = $idPath;
        $this->labelPath = $labelPath;
        $this->suggestionsFetcher = $suggestionsFetcher;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->entityManagerName = $entityManagerName;
    }

    /**
     * Calls the custom suggestions fetcher and returns the result.
     *
     * @param string $term
     *
     * @return array
     *
     * @throws \LogicException
     * - if the suggestions fetcher isn't well defined;
     */
    protected function callSuggestionsFetcher($term)
    {
        /* @var $entityRepository EntityRepository */
        $entityRepository = $this->getEntityManager()->getRepository($this->entityClass);

        if (is_string($this->suggestionsFetcher) && is_callable(array($entityRepository, $this->suggestionsFetcher))) {
            return call_user_func(array($entityRepository, $this->suggestionsFetcher), $term, $this->suggestionsLimit);
        } elseif (is_callable($this->suggestionsFetcher)) {
            return call_user_func($this->suggestionsFetcher, $term, $this->suggestionsLimit);
        } else {
            throw new \LogicException(
                'The suggestions fetcher may be either a string pointing to a repository method or a callable!'
            );
        }
    }

    /**
     * Fetches the suggestions raw data.
     *
     * @param string $term
     *
     * @return array
     */
    protected function getSuggestionsData($term)
    {
        // Try to call the custom fetcher method if provided.
        if (null !== $this->suggestionsFetcher) {
            return $this->callSuggestionsFetcher($term);
        }

        // Build the default query. We will compare the entity labels with the term and fetch the matches.
        $classIndex = strrpos($this->entityClass, '\\');
        $entityAlias = (false === $classIndex) ? $this->entityClass : substr($this->entityClass, $classIndex + 1);
        $entityAlias = strtolower($entityAlias);

        return $this->getEntityManager()
            ->getRepository($this->entityClass)
            ->createQueryBuilder($entityAlias)
            ->where("$entityAlias.$this->labelPath LIKE :term")
            ->setParameter(':term', "%$term%")
            ->setMaxResults($this->suggestionsLimit)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getSuggestions($term, $context = null)
    {
        // Fetch the suggestions raw data.
        $data = $this->getSuggestionsData($term);

        $suggestions = array();
        foreach ($data as $item) {
            $suggestions[] = array(
                'id' => $this->propertyAccessor->getValue($item, $this->idPath),
                'text' => $this->propertyAccessor->getValue($item, $this->labelPath),
            );
        }

        return $suggestions;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuggestionsLimit($suggestionsLimit)
    {
        $this->suggestionsLimit = $suggestionsLimit;

        return $this;
    }

    /**
     * Returns the entity manager to use for fetching data.
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->doctrine->getManager($this->entityManagerName);
    }
}

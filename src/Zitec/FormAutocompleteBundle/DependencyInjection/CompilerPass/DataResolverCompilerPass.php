<?php

namespace Zitec\FormAutocompleteBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Zitec\FormAutocompleteBundle\DataResolver\DataResolverInterface;
use Zitec\FormAutocompleteBundle\DataResolver\LimitAwareDataResolverInterface;

/**
 * Compiler pass which has the responsibility of configuring and registering all the data resolvers declared in the
 * container into the data resolver manager. In order to declare a data resolver, the user must create a service that
 * implements the DataResolverInterface, tag it and set an attribute on the tag which specifies the data resolver key.
 */
class DataResolverCompilerPass implements CompilerPassInterface
{
    /**
     * The name of the tag which a service must have in order to be considered a data resolver.
     */
    const DATA_RESOLVER_TAG = 'zitec_autocomplete_data_resolver';

    /**
     * The id of the data resolver manager service.
     */
    const DATA_RESOLVER_MANAGER_ID = 'zitec.form_autocomplete.data_resolver_manager';

    /**
     * The name of the parameter containing the global suggestions limit.
     */
    const SUGGESTIONS_LIMIT_PARAMETER = 'zitec.form_autocomplete.suggestions_limit';

    /**
     * Determines the key of a data resolver given the corresponding tags.
     *
     * @param string $serviceId
     * @param array $tags
     *
     * @return string
     *
     * @throws LogicException
     * - if the key attribute wasn't found on the tags;
     */
    protected function getDataResolverKey($serviceId, $tags)
    {
        foreach ($tags as $tag) {
            if (!empty($tag['key'])) {
                return $tag['key'];
            }
        }

        throw new LogicException(sprintf(
            'You must define a key for the data resolver with the id: %s!',
            $serviceId
        ));
    }

    /**
     * Checks if the class of a data resolver implements the data resolver interface.
     *
     * @param ContainerBuilder $container
     * @param string $serviceId
     *
     * @throws LogicException
     */
    protected function validateDataResolverClass(ContainerBuilder $container, $serviceId)
    {
        $definition = $container->getDefinition($serviceId);
        $class = $container->getParameterBag()->resolveValue($definition->getClass());
        $reflection = new \ReflectionClass($class);

        if (!$reflection->implementsInterface(DataResolverInterface::class)) {
            throw new LogicException(sprintf(
                'The data resolver with the id "%s" should implement the %s interface!',
                $serviceId,
                DataResolverInterface::class
            ));
        }
    }

    /**
     * Determines the suggestions limit for a limit aware data resolver.
     *
     * @param array $tags
     *
     * @return int|Parameter
     *
     * @throws LogicException If the suggestions limit specified on a tag is not a positive integer.
     */
    protected function getSuggestionsLimit(array $tags)
    {
        foreach ($tags as $tag) {
            if (empty($tag['suggestions_limit'])) {
                continue;
            }

            if (!is_int($tag['suggestions_limit']) || $tag['suggestions_limit'] <= 0) {
                throw new LogicException('The suggestions limit must be an integer positive number!');
            }

            return $tag['suggestions_limit'];
        }

        return new Parameter(self::SUGGESTIONS_LIMIT_PARAMETER);
    }

    /**
     * Configures the suggestions limit for limit aware data resolvers.
     *
     * @param ContainerBuilder $container
     * @param string $serviceId
     * @param array $tags
     */
    protected function configureSuggestionsLimit(ContainerBuilder $container, $serviceId, array $tags)
    {
        $definition = $container->getDefinition($serviceId);
        $class = $container->getParameterBag()->resolveValue($definition->getClass());
        $reflection = new \ReflectionClass($class);

        if ($reflection->implementsInterface(LimitAwareDataResolverInterface::class)) {
            $definition->addMethodCall('setSuggestionsLimit', [$this->getSuggestionsLimit($tags)]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Collect the defined data resolvers.
        $dataResolvers = array();
        $dataResolverTags = $container->findTaggedServiceIds(self::DATA_RESOLVER_TAG);
        foreach ($dataResolverTags as $serviceId => $tags) {
            // Determine the key of the data resolver.
            $key = $this->getDataResolverKey($serviceId, $tags);
            // Validate if the key is unique.
            if (isset($dataResolvers[$key])) {
                throw new LogicException(sprintf('Duplicate data resolver key "%s" found!', $key));
            }

            // Validate the class of the data resolver.
            $this->validateDataResolverClass($container, $serviceId);

            // Configure the suggestions limit for limit aware data resolvers.
            $this->configureSuggestionsLimit($container, $serviceId, $tags);

            $dataResolvers[$key] = new Reference($serviceId);
        }

        // Register the data resolvers to the manager.
        $dataResolverManager = $container->getDefinition(self::DATA_RESOLVER_MANAGER_ID);
        $dataResolverManager->setArguments(array($dataResolvers));
    }
}

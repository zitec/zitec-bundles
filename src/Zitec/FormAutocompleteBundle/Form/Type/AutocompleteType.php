<?php

namespace Zitec\FormAutocompleteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Zitec\FormAutocompleteBundle\DataResolver\DataResolverManager;
use Zitec\FormAutocompleteBundle\Form\DataTransformer\AutocompleteDataTransformer;

/**
 * Defines the zitec autocomplete form field type. This field will be basically a text box with suggestions
 * generated from the user input.
 */
class AutocompleteType extends AbstractType
{
    /**
     * The default autocomplete suggestions provider path.
     */
    public const DEFAULT_AUTOCOMPLETE_PATH = 'zitec_form_autocomplete_autocomplete';

    /**
     * The routing service.
     */
    protected RouterInterface $router;

    /**
     * The data resolver manager service.
     */
    protected DataResolverManager $dataResolverManager;

    public function __construct(RouterInterface $router, DataResolverManager $dataResolverManager)
    {
        $this->router = $router;
        $this->dataResolverManager = $dataResolverManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Attach the data transformer.
        $dataResolver = $this->dataResolverManager->get($options['data_resolver']);
        $builder->addViewTransformer(new AutocompleteDataTransformer($dataResolver, $options['compatibility']));

        // The default value of the field will be an empty array if it allows multiple values.
        // Otherwise, it will be an empty string.
        if ($options['multiple']) {
            $builder->setEmptyData([]);
        } else {
            $builder->setEmptyData('');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Define a function which tests against the boolean value true. It will be used to validate the value
        // of the compound option, which should always be false.
        $validateNotTrue = function ($value) {
            return true !== $value;
        };

        // Define a function which tests against non-negative numbers. It will be used to validate the values
        // of the delay and minimum_input_length options.
        $validateNotNegative = function ($value) {
            return $value >= 0;
        };

        // Define a function which validates the value of the allow_clear option.
        $allowClearValidator = function (Options $options, $value) {
            if (!$value) {
                return $value;
            }

            if ($options['compatibility']) {
                if ($options['multiple']) {
                    throw new InvalidOptionsException(
                        'On compatibility mode you cannot set the allow_clear option to true for multi-value fields!'
                    );
                }

                if (null === $options['placeholder'] || '' === $options['placeholder']) {
                    throw new InvalidOptionsException(
                        'On compatibility mode you cannot set the allow_clear option to true for single-value fields '
                        . 'if the placeholder is empty!'
                    );
                }
            } elseif (!$options['multiple'] && null === $options['placeholder']) {
                throw new InvalidOptionsException(
                    'On the default mode you cannot set the allow_clear option to true for single-value fields '
                    . 'if the placeholder is undefined!'
                );
            }

            return $value;
        };

        $resolver->setRequired(['data_resolver'])
            ->setDefaults([
                'compound' => false,
                'autocomplete_path' => null,
                'multiple' => false,
                'placeholder' => null,
                'delay' => 0,
                'minimum_input_length' => 1,
                'allow_clear' => false,
                'other_select2_options' => null,
                'context' => null,
                // Must be set to true if the used Select2 version is lower than 4.0.0.
                'compatibility' => false,
                'dropdownParent' => false
            ])
            ->setAllowedTypes('data_resolver', 'string')
            ->setAllowedTypes('autocomplete_path', ['null', 'string'])
            ->setAllowedTypes('multiple', 'boolean')
            ->setAllowedTypes('placeholder', ['null', 'string'])
            ->setAllowedTypes('delay', 'integer')
            ->setAllowedTypes('minimum_input_length', 'integer')
            ->setAllowedTypes('allow_clear', 'boolean')
            ->setAllowedTypes('other_select2_options', ['array', 'object', 'null'])
            ->setAllowedTypes('compatibility', 'boolean')
            ->setAllowedValues('compound', $validateNotTrue)
            ->setAllowedValues('delay', $validateNotNegative)
            ->setAllowedValues('minimum_input_length', $validateNotNegative)
            ->setNormalizer('allow_clear', $allowClearValidator);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // The user may provide a custom autocomplete path.
        if (null !== $options['autocomplete_path']) {
            $view->vars['autocomplete_path'] = $this->router->generate($options['autocomplete_path']);
        } else {
            $view->vars['autocomplete_path'] = $this->router->generate(
                self::DEFAULT_AUTOCOMPLETE_PATH,
                ['dataResolverId' => $options['data_resolver']]
            );
        }

        $view->vars['multiple'] = $options['multiple'];
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['delay'] = $options['delay'];
        $view->vars['minimum_input_length'] = $options['minimum_input_length'];
        $view->vars['allow_clear'] = $options['allow_clear'];
        $view->vars['other_select2_options'] = $options['other_select2_options'];
        $view->vars['context'] = $options['context'];
        $view->vars['compatibility'] = $options['compatibility'];
        $view->vars['dropdownParent'] = $options['dropdownParent'];
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        // Make PHP interpret the request parameter as an array if the field allows multiple values.
        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';
        }
    }

    public function getName(): string
    {
        return 'zitec_autocomplete';
    }

    public function getBlockPrefix(): string
    {
        return $this->getName();
    }
}

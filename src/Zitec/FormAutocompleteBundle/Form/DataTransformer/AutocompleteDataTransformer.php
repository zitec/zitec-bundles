<?php

namespace Zitec\FormAutocompleteBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Zitec\FormAutocompleteBundle\DataResolver\DataResolverInterface;

/**
 * The data transformer specific to the autocomplete form field type. It will use the data resolver specific
 * to the currently handled field.
 */
class AutocompleteDataTransformer implements DataTransformerInterface
{
    /**
     * An autocomplete data resolver instance which will perform the data transformations.
     *
     * @var DataResolverInterface
     */
    protected DataResolverInterface $dataResolver;

    /**
     * Flag which marks if the data from the view will always be represented as a string (even when the field carries
     * multiple values). The information will be propagated to the data resolver in order to format the view
     * data accordingly.
     *
     * @var bool
     */
    protected bool $viewDataAlwaysString;

    /**
     * The data transformer constructor.
     *
     * @param DataResolverInterface $dataResolver
     * @param bool $viewDataAlwaysString
     */
    public function __construct(DataResolverInterface $dataResolver, bool $viewDataAlwaysString = false)
    {
        $this->dataResolver = $dataResolver;
        $this->viewDataAlwaysString = $viewDataAlwaysString;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        try {
            return $this->dataResolver->getViewData($value);
        } catch (\Exception $exception) {
            throw new TransformationFailedException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        try {
            return $this->dataResolver->getModelData($value, $this->viewDataAlwaysString);
        } catch (\Exception $exception) {
            throw new TransformationFailedException($exception->getMessage(), 0, $exception);
        }
    }
}

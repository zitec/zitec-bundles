<?php

namespace Zitec\FormAutocompleteBundle\DataResolver;

/**
 * Data resolver which relates the data of a single-value autocomplete field to an entity. Programmers may use
 * directly this class in order to declare their data-resolver services.
 */
class EntitySingleDataResolver extends EntityBaseDataResolver
{
    /**
     * {@inheritdoc}
     */
    public function getModelData($viewData, $viewDataAlwaysString = false)
    {
        // Validate the received data.
        if (!is_scalar($viewData)) {
            throw new \InvalidArgumentException('Expected a scalar in order to fetch the related model!');
        }

        // Verify if the client sent data related to the field.
        if ('' === $viewData) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository($this->entityClass)
            ->find($viewData);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData($modelData)
    {
        if (null === $modelData) {
            return null;
        }

        return array(
            'value' => $this->propertyAccessor->getValue($modelData, $this->idPath),
            'label' => $this->propertyAccessor->getValue($modelData, $this->labelPath),
        );
    }
}

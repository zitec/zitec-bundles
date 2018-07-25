<?php

namespace Zitec\FormAutocompleteBundle\DataResolver;

/**
 * Data resolver which relates the data of a multiple-value autocomplete field to an entity. Programmers may use
 * directly this class in order to declare their data-resolver services.
 */
class EntityMultipleDataResolver extends EntityBaseDataResolver
{
    /**
     * {@inheritdoc}
     */
    public function getModelData($viewData, $viewDataAlwaysString = false)
    {
        // Validate the received data.
        if (!is_array($viewData)) {
            throw new \InvalidArgumentException('Expected an array in order to fetch the related models!');
        }

        // Verify if the client sent data related to the field.
        if (empty($viewData)) {
            return array();
        }

        // Parse the view data.
        if ($viewDataAlwaysString) {
            $rawData = reset($viewData);
            $viewData = explode(',', $rawData);
        }

        $data = $this->getEntityManager()
            ->getRepository($this->entityClass)
            ->findBy(array($this->idPath => $viewData));

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData($modelData)
    {
        if (null === $modelData) {
            return array();
        }

        // Parse the model collection and extract the data.
        $data = array();
        foreach ($modelData as $item) {
            $data[] = array(
                'value' => $this->propertyAccessor->getValue($item, $this->idPath),
                'label' => $this->propertyAccessor->getValue($item, $this->labelPath),
            );
        }

        return $data;
    }
}

<?php

namespace Zitec\FormAutocompleteBundle\DataResolver;

/**
 * Data resolver which relates the data of a multiple-value autocomplete field to an entity. Programmers may use
 * directly this class in order to declare their data-resolver services.
 */
class EntityMultipleDataResolver extends EntityBaseDataResolver
{
    public function getModelData($viewData, bool $viewDataAlwaysString = false)
    {
        // Validate the received data.
        if (!is_array($viewData)) {
            throw new \InvalidArgumentException('Expected an array in order to fetch the related models!');
        }

        // Verify if the client sent data related to the field.
        if (empty($viewData)) {
            return [];
        }

        // Parse the view data.
        if ($viewDataAlwaysString) {
            $rawData = reset($viewData);
            $viewData = explode(',', $rawData);
        }

        return $this->doctrine
            ->getRepository($this->entityClass)
            ->findBy([$this->idPath => $viewData]);
    }

    public function getViewData($modelData): array
    {
        if (null === $modelData) {
            return [];
        }

        // Parse the model collection and extract the data.
        $data = [];
        foreach ($modelData as $item) {
            $data[] = [
                'value' => $this->propertyAccessor->getValue($item, $this->idPath),
                'label' => $this->propertyAccessor->getValue($item, $this->labelPath),
            ];
        }

        return $data;
    }
}

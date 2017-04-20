<?php

namespace Zitec\TranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The ZitecTranslationBundle definition. This bundle provides enhancements for the Symfony Translation component.
 */
class ZitecTranslationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * @throws \LogicException If the dependencies aren't met.
     */
    public function boot()
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        if (empty($bundles['JMSTranslationBundle'])) {
            throw new \LogicException(sprintf('%s requires JMSTranslationBundle!', $this->getName()));
        }
    }
}

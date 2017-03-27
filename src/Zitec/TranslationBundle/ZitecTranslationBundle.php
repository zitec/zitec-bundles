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
     */
    public function boot()
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        if (empty($bundles['JMSTranslationBundle'])) {
            throw new \RuntimeException('In order to use this bundle, you should install the JMSTranslationBundle!');
        }
    }
}

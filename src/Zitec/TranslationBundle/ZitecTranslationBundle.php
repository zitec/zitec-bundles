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
    public function getParent()
    {
        return 'JMSTranslationBundle';
    }
}

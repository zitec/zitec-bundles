<?php

namespace Zitec\FormAutocompleteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zitec\FormAutocompleteBundle\DependencyInjection\CompilerPass\DataResolverCompilerPass;

class ZitecFormAutocompleteBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DataResolverCompilerPass());
    }
}

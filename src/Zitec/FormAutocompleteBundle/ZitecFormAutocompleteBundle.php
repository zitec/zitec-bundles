<?php

declare(strict_types=1);

namespace Zitec\FormAutocompleteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zitec\FormAutocompleteBundle\DependencyInjection\CompilerPass\DataResolverLoaderCompilerPass;

class ZitecFormAutocompleteBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DataResolverLoaderCompilerPass());
    }
}

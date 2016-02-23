<?php

namespace MandarinMedien\MMCmfContentBundle;

use MandarinMedien\MMCmfContentBundle\DependencyInjection\Compiler\ContentCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MMCmfContentBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ContentCompilerPass());
    }

}

<?php

namespace MandarinMedien\MMCmfContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ContentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('mm_cmf_content.page_hook_manager')) {
            return;
        }

        $definition = $container->findDefinition(
            'mm_cmf_content.page_hook_manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'mm_cmf_content.page_hook'
        );


        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addHook',
                array(new Reference($id))
            );
        }
    }
}
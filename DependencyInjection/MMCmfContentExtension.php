<?php

namespace MandarinMedien\MMCmfContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MMCmfContentExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = array();

        foreach ($configs as $subConfig) {
            $config = array_merge_recursive($config, $subConfig);
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, array($config));

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureContentNodes($container,$config);

    }


    public function configureContentNodes(ContainerBuilder $container, $config)
    {
        $container->getDefinition('mm_cmf_content.content_parser')
            ->replaceArgument(0, $config['content_nodes']);
    }
}

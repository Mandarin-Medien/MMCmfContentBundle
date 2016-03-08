<?php

namespace MandarinMedien\MMCmfContentBundle\Twig;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use Symfony\Component\DependencyInjection\Container;

class NodeTreeRenderExtension extends \Twig_Extension
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('renderNodeTree', array($this, "renderNodeTreeFunction"), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('renderNodeTreeItem', array($this, "renderNodeTreeItemFunction"), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            ))
        );
    }

    /**
     * renders the menu
     *
     * @param \Twig_Environment $twig
     * @param NodeInterface $node
     * @param array $options
     * @return string
     */
    public function renderNodeTreeFunction(\Twig_Environment $twig, NodeInterface $node, array $options=array())
    {
        return $twig->render('MMCmfContentBundle:Form/NodeTree:list.html.twig', array('node' => $node, 'options' => $options));
    }

    public function renderNodeTreeItemFunction(\Twig_Environment $twig, NodeInterface $node, array $options=array())
    {
        return $twig->render('MMCmfContentBundle:Form/NodeTree:item.html.twig', array('node' => $node, 'options' => $options));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mm_cmf_content_node_tree_extension';
    }
}
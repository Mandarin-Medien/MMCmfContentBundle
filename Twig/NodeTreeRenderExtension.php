<?php

namespace MandarinMedien\MMCmfContentBundle\Twig;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use Symfony\Component\DependencyInjection\Container;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NodeTreeRenderExtension extends AbstractExtension
{

    private $container;
    private $contentNodeParser;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->contentNodeParser = $this->container->get('mm_cmf_content.content_parser');
    }


    /**
     * {@inheritdoc}
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('renderNodeTree', array($this, "renderNodeTreeFunction"), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
            new TwigFunction('renderNodeTreeItem', array($this, "renderNodeTreeItemFunction"), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            ))
        );
    }

    /**
     * renders the menu
     *
     * @param Environment $twig
     * @param NodeInterface $node
     * @param array $options
     * @return string
     */
    public function renderNodeTreeFunction(Environment $twig, NodeInterface $node, array $options=array())
    {
        return $twig->render('@MMCmfContent/Form/NodeTree/list.html.twig', array('node' => $node, 'icon' => $this->getIcon(get_class($node)), 'options' => $options));
    }

    public function renderNodeTreeItemFunction(Environment $twig, NodeInterface $node, array $options=array())
    {
        return $twig->render('@MMCmfContent/Form/NodeTree/item.html.twig', array('node' => $node, 'icon' => $this->getIcon(get_class($node)), 'options' => $options));
    }

    function getIcon($node)
    {
        return $this->contentNodeParser->getIcon($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mm_cmf_content_node_tree_extension';
    }
}
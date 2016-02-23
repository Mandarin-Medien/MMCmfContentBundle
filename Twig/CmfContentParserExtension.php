<?php

namespace MandarinMedien\MMCmfContentBundle\Twig;

use MandarinMedien\MMCmfContentBundle\Controller\ContentParserController;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;

class CmfContentParserExtension extends \Twig_Extension
{
    /**
     * @var ContentParserController
     */
    protected $cmfContentParser;

    public function __construct(ContentParserController $cmfContentParser = null)
    {
        $this->cmfContentParser = $cmfContentParser;
    }

    /**
     * registers the twig filter
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cmfParse',
                array($this, 'cmfParse'),
                array(
                    'is_safe' => array('html'),
                    'needs_environment' => true
                )
            ),
        );
    }

    /**
     * @param \Twig_Environment $twig
     * @param Node $node
     * @param array $options
     *
     * @return string
     */
    public function cmfParse(\Twig_Environment $twig, Node $node, array $options = array())
    {
        $html = "";

        if($node instanceof ContentNode)
        {
            /**
             * @var ContentNode $node
             */
            $template = $this->cmfContentParser->findTemplate($node);

            $refClass = $this->cmfContentParser->getNativeClassnamimg($node);

            $className = $refClass['name'];

            $html = $twig->render($template, array_merge_recursive(array('node' => $node,'node_class'=> $className), $options));
        }

        return $html;


    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mm_cmf_content_parser_twig_extension';
    }

}
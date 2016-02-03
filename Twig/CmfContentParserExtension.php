<?php

namespace MandarinMedien\MMCmfContentBundle\Twig;

use MandarinMedien\MMCmfContentBundle\Controller\ContentParserController;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;

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
     * @param ContentNode $node
     * @param array $options
     *
     * @return string
     */
    public function cmfParse(\Twig_Environment $twig, ContentNode $node, array $options = array())
    {
        /**
         * @var ContentNode $childNode
         */
        $template = $this->cmfContentParser->findTemplate($node);

        $refClass = $this->cmfContentParser->getNativeClassnamimg($node);

        $className = $refClass['name'];

        return $twig->render($template, array_merge_recursive(array('node' => $node,'node_class'=> $className), $options));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mm_cmf_content_parser_twig_extension';
    }

}
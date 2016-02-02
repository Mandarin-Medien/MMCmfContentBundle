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
     *
     * @return string
     */
    public function cmfParse(\Twig_Environment $twig, ContentNode $node)
    {
        $html = "";

        foreach ($node->getNodes() as $childNode) {
            $template = $this->cmfContentParser->findTemplate($childNode);

            $html .= $twig->render($template, array('node' => $childNode));
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
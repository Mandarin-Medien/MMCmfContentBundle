<?php

namespace MandarinMedien\MMCmfContentBundle\Twig;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;

class CmfContentParserExtension extends \Twig_Extension
{
    function __construct()
    {

    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cmfParse', array($this, 'cmfParse'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
        );
    }

    /**
     * @param ContentNode $node
     * @return string
     */
    public function cmfParse(\Twig_Environment $twig,ContentNode $node)
    {
        $html = "";

        foreach($node->getNodes() as $childNode)
        {
            $html .= $twig->render($this->getTemplate($childNode),array('node'=>$childNode));
        }

        return $html;

    }

    public function getName()
    {
        return 'mm_cmf_content_parser_twig_extension';
    }

    public function getTemplate(ContentNode $node)
    {
        $template = $node->getTemplate();

        return $template;

    }
}
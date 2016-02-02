<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContentParserController extends Controller
{
    private $contentNodeTemplates;

    public function __construct(Array $contentNodeConfig)
    {
        $this->parseContentNodeConfig($contentNodeConfig);

    }

    private function parseContentNodeConfig($contentNodeConfig)
    {
        foreach ($contentNodeConfig as $nodeName => $nodeAttributes) {

            $className = ucfirst($nodeName);

            $this->contentNodeTemplates[$className] = array();

            foreach ($nodeAttributes['templates'] as $templateItem) {
                $this->contentNodeTemplates[$className][$templateItem['name']] = $templateItem['template'];
            }

        }

    }


    public function findTemplate(ContentNode $node)
    {
        //check if the node is already related to a template
        $template = $node->getTemplate();

        // if no template is set try to guess one
        if (!$template) {

            $refClass = new \ReflectionClass($node);
            $className = trim(str_replace($refClass->getNamespaceName(),'',$refClass->getName()),'\\');


            if (!empty($this->contentNodeTemplates[$className]) && count($this->contentNodeTemplates[$className]) > 0)
                $template = reset($this->contentNodeTemplates[$className]);
            else {

                $bundleName = $this->getBundleNameFromEntity($refClass->getNamespaceName(), $this->get('kernel')->getBundles());

                $template = $this->getDefaultTemplate($className,$bundleName);
            }

        }

        return $template;

    }

    /**
     * Get the bundle name from an Entity namespace
     *
     * @param $entityNamespace
     * @param $bundles
     * @return int|string
     */
    protected static function getBundleNameFromEntity($entityNamespace, $bundles)
    {
        $dataBaseNamespace = substr($entityNamespace, 0, strpos($entityNamespace, '\\Entity'));

        foreach ($bundles as $type => $bundle) {
            $bundleRefClass = new \ReflectionClass($bundle);
            if ($bundleRefClass->getNamespaceName() === $dataBaseNamespace) {
                return $type;
            }
        }
    }

    /**
     * @param string $bundleName
     * @return string
     */
    public function getDefaultTemplate($className ,$bundleName = "MMCmfContentBundle")
    {
        return $bundleName . ':cmf:' . $className . '/' . $className . '_default.html.twig';
    }

}

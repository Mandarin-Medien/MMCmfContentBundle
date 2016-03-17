<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;

/**
 * @todo: may need a new name couz of new purpose
 *
 * Class ContentParserController
 * @package MandarinMedien\MMCmfContentBundle\Controller
 */
class ContentParserController
{

    protected $container;


    private $contentNodeHiddenFields = array();
    private $contentNodeSimpleForms = array();
    private $contentNodeIcons = array();
    protected $templateManager;

    public function __construct(Container $container, Array $contentNodeConfig)
    {

        $this->container = $container;
        $this->templateManager = $this->container->get('mm_cmf_content.template_manager');

        $this->parseContentNodeConfig($contentNodeConfig);
    }

    private function parseContentNodeConfig($contentNodeConfig)
    {
        foreach ($contentNodeConfig as $nodeName => $nodeAttributes) {

            $className = ucfirst($nodeName);

            // templates
           /* $this->contentNodeTemplates[$className] = array();

            if (isset($nodeAttributes['templates']))
                foreach ($nodeAttributes['templates'] as $templateItem) {
                    $this->contentNodeTemplates[$className][$templateItem['name']] = $templateItem['template'];
                }*/

            // hiddenFields
            $this->contentNodeHiddenFields[$className] = array();

            if (isset($nodeAttributes['hiddenFields']))
                foreach ($nodeAttributes['hiddenFields'] as $field) {
                    $this->contentNodeHiddenFields[$className][] = $field;
                }

            // simpleFormType
            $this->contentNodeSimpleForms[$className] = array();

            if (isset($nodeAttributes['simpleForm']))
                foreach ($nodeAttributes['simpleForm'] as $key => $field) {
                    $this->contentNodeSimpleForms[$className][$key] = $field;
                }

            // icons
            $this->contentNodeSimpleForms[$className] = array();

            if (isset($nodeAttributes['icon']))
                $this->contentNodeIcons[$className] = $nodeAttributes['icon'];
            else
                $this->contentNodeIcons[$className] = 'fa fa-file-o';

        }
    }

    /**
     * @param $node
     * @return array|null
     */
    public function getIcon($node)
    {
        $classNameing = $this->getNativeClassnamimg($node);

        return (isset($this->contentNodeIcons[$classNameing['name']])) ? $this->contentNodeIcons[$classNameing['name']] : '';
    }

    /**
     * @param $node
     * @return array|null
     */
    public function getSimpleForm($node)
    {
        $classNameing = $this->getNativeClassnamimg($node);

        return (isset($this->contentNodeSimpleForms[$classNameing['name']])) ? $this->contentNodeSimpleForms[$classNameing['name']] : null;
    }

    /**
     * @param $node
     * @return array
     */
    public function getHiddenFields($node)
    {
        $classNameing = $this->getNativeClassnamimg($node);

        return (isset($this->contentNodeHiddenFields[$classNameing['name']])) ? $this->contentNodeHiddenFields[$classNameing['name']] : array();
    }

    /**
     * @param $node
     * @return array
     */
    public function getTemplates($node)
    {
        //$classNameing = $this->getNativeClassnamimg($node);

        $this->templateManager->getTemplates(get_class($node));


        //return (isset($this->contentNodeTemplates[$classNameing['name']])) ? $this->contentNodeTemplates[$classNameing['name']] : null;
    }

    /**
     * @param $node
     * @return array
     */
    public function getNativeClassnamimg($node)
    {
        $refClass = new \ReflectionClass($node);
        $className = trim(str_replace($refClass->getNamespaceName(), '', $refClass->getName()), '\\');

        return array('name' => $className, 'namespace' => $refClass->getNamespaceName());
    }

    /**
     * @param ContentNode $node
     * @return mixed|string
     */
    public function findTemplate(ContentNode $node)
    {
        return $this->templateManager->getTemplate($node);

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
    public function getDefaultTemplate($className, $bundleName = "MMCmfContentBundle")
    {
        return $bundleName . ':cmf:' . $className . '/' . $className . '_default.html.twig';
    }

}

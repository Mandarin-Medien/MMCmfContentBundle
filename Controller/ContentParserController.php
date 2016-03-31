<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Node\NodeInterface;
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
    private $contentNodeGridable = array();

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
            $this->contentNodeIcons[$className] = array();

            if (isset($nodeAttributes['icon']))
                $this->contentNodeIcons[$className] = $nodeAttributes['icon'];
            else
                $this->contentNodeIcons[$className] = 'fa fa-file-o';

            // contentNodeGridable
            $this->contentNodeGridable[$className] = array();

            if (isset($nodeAttributes['gridable']))
            {
                $this->contentNodeGridable[$className] = $nodeAttributes['gridable'];
            }
            else
                $this->contentNodeGridable[$className] = true;
        }
    }

    /**
     * @param $nodeClassName
     * @return bool
     */
    public function isGridable($nodeClassName)
    {
        return (isset($this->contentNodeGridable[$nodeClassName])) ? $this->contentNodeGridable[$nodeClassName] : true;
    }

    /**
     *
     * @return array
     */
    public function getNotGridableClasses()
    {
        $classes = array();
        foreach($this->contentNodeGridable as $key => $val)
            if(!$val)
                $classes[] = $key;

        return $classes;
    }


    /**
     * @param $nodeClassName
     * @return string
     */
    public function getIcon($nodeClassName)
    {
        return (isset($this->contentNodeIcons[$nodeClassName])) ? $this->contentNodeIcons[$nodeClassName] : '';
    }

    /**
     * @param $nodeClassName
     * @return array|null
     */
    public function getSimpleForm($nodeClassName)
    {
        return (isset($this->contentNodeSimpleForms[$nodeClassName])) ? $this->contentNodeSimpleForms[$nodeClassName] : null;
    }

    /**
     * @param $nodeClassName
     * @return array
     */
    public function getHiddenFields($nodeClassName)
    {

        return (isset($this->contentNodeHiddenFields[$nodeClassName])) ? $this->contentNodeHiddenFields[$nodeClassName] : array();
    }

    /**
     * @deprecated Look at the new MandarinMedien\MMCmfContentBundle\Templating\TemplateManager.
     *
     * @param $nodeClassName
     *
     * @return array
     */
    public function getTemplates($nodeClassName)
    {
        return $this->templateManager->getTemplates($nodeClassName);
    }

    /**
     * @param $node NodeInterface
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

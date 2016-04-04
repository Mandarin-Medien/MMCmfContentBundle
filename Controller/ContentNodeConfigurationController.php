<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Node\NodeInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * @todo: may need a new name couz of new purpose
 *
 * Class ContentNodeConfigurationController
 * @package MandarinMedien\MMCmfContentBundle\Controller
 */
class ContentNodeConfigurationController
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $contentNodeTypes = array();

    /**
     * @var \MandarinMedien\MMCmfContentBundle\Templating\TemplateManager
     */
    protected $templateManager;

    /**
     * ContentNodeConfigurationController constructor.
     *
     * @param Container $container
     * @param array $contentNodeConfig
     */
    public function __construct(Container $container, Array $contentNodeConfig)
    {
        $this->container = $container;
        $this->templateManager = $this->container->get('mm_cmf_content.template_manager');

        $this->parseContentNodeConfig($contentNodeConfig);
    }

    /**
     * @param $contentNodeConfig
     */
    private function parseContentNodeConfig($contentNodeConfig)
    {

        $content_node_factory = $this->container->get('mm_cmf_content.content_node_factory');

        foreach ($contentNodeConfig as $nodeClassName => $nodeAttributes) {

            /**
             * new solution to hold configuartions
             */

            $this->contentNodeTypes[$nodeClassName] = $nodeAttributes;
            $this->contentNodeTypes[$nodeClassName]['name'] = $nodeClassName;
            $this->contentNodeTypes[$nodeClassName]['discriminator'] = $content_node_factory->getDiscriminatorByClassName($nodeClassName);

            /**
             * fields to hide
             */
            if (isset($nodeAttributes['hiddenFields']))
                foreach ($nodeAttributes['hiddenFields'] as $field) {
                    $this->contentNodeTypes[$nodeClassName]['hiddenFields'][] = $field;
                }

            /**
             * simpleform
             */
            if (isset($nodeAttributes['simpleForm']))
                foreach ($nodeAttributes['simpleForm'] as $key => $field) {
                    $this->contentNodeTypes[$nodeClassName]['simpleForm'][$key] = $field;
                }

            /**
             * icon
             */
            if (isset($nodeAttributes['icon']))
                $this->contentNodeTypes[$nodeClassName]['icon'] = $nodeAttributes['icon'];
            else
                $this->contentNodeTypes[$nodeClassName]['icon'] = 'fa fa-file-o';

            /**
             * gridable
             */
            if (isset($nodeAttributes['gridable'])) {
                $this->contentNodeTypes[$nodeClassName]['gridable'] = $nodeAttributes['gridable'];
            } else
                $this->contentNodeTypes[$nodeClassName]['gridable'] = true;
        }
    }

    /**
     * @param $nodeClassName
     * @return array|null
     */
    function getContentNodeType($nodeClassName)
    {
        return (isset($this->contentNodeTypes[$nodeClassName])) ? $this->contentNodeTypes[$nodeClassName] : null;
    }

    /**
     * @return array[]
     */
    function getContentNodeTypes()
    {
        return $this->contentNodeTypes;
    }

    /**
     * @param $nodeClassName
     * @return bool
     */
    public function isGridable($nodeClassName)
    {
        return (isset($this->contentNodeTypes[$nodeClassName]['gridable'])) ? $this->contentNodeTypes[$nodeClassName]['gridable'] : true;
    }

    /**
     *
     * @return array
     */
    public function getNotGridableClasses()
    {
        $classes = array();
        foreach ($this->contentNodeTypes as $nodeClassName => $attr)
            if (!$this->isGridable($nodeClassName))
                $classes[] = $nodeClassName;

        return $classes;
    }


    /**
     * @param $nodeClassName
     * @return string
     */
    public function getIcon($nodeClassName)
    {
        return (isset($this->contentNodeTypes[$nodeClassName]['icon'])) ? $this->contentNodeTypes[$nodeClassName]['icon'] : '';
    }

    /**
     * @param $nodeClassName
     * @return array|null
     */
    public function getSimpleForm($nodeClassName)
    {
        return (isset($this->contentNodeTypes[$nodeClassName]['simpleForm'])) ? $this->contentNodeTypes[$nodeClassName]['simpleForm'] : null;
    }

    /**
     * @param $nodeClassName
     * @return array
     */
    public function getHiddenFields($nodeClassName)
    {

        return (isset($this->contentNodeTypes[$nodeClassName]['hiddenFields'])) ? $this->contentNodeTypes[$nodeClassName]['hiddenFields'] : array();
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
     * @deprecated Look at the new MandarinMedien\MMCmfContentBundle\Templating\TemplateManager.
     *
     * @param string $bundleName
     * @return string
     */
    public function getDefaultTemplate($className, $bundleName = "MMCmfContentBundle")
    {
        return $this->templateManager->getDefaultTemplate($className, $bundleName);
    }
}

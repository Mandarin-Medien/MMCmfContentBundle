<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @todo: may need a new name couz of new purpose
 *
 * Class ContentParserController
 * @package MandarinMedien\MMCmfContentBundle\Controller
 */
class ContentParserController extends Controller
{
    private $contentNodeTemplates = array();
    private $contentNodeHiddenFields = array();
    private $contentNodeSimpleForm = array();

    public function __construct(Array $contentNodeConfig)
    {
        $this->parseContentNodeConfig($contentNodeConfig);

    }

    private function parseContentNodeConfig($contentNodeConfig)
    {
        foreach ($contentNodeConfig as $nodeName => $nodeAttributes) {

            $className = ucfirst($nodeName);

            // templates
            $this->contentNodeTemplates[$className] = array();

            if (isset($nodeAttributes['templates']))
                foreach ($nodeAttributes['templates'] as $templateItem) {
                    $this->contentNodeTemplates[$className][$templateItem['name']] = $templateItem['template'];
                }

            // hiddenFields
            $this->contentNodeHiddenFields[$className] = array();

            if (isset($nodeAttributes['hiddenFields']))
                foreach ($nodeAttributes['hiddenFields'] as $field) {
                    $this->contentNodeHiddenFields[$className][] = $field;
                }

            // simpleFormType
            $this->contentNodeSimpleFormType[$className] = array();

            if (isset($nodeAttributes['simpleForm']))
                foreach ($nodeAttributes['simpleForm'] as $key => $field) {
                    $this->contentNodeSimpleForm[$className][$key] = $field;
                }
        }
    }

    /**
     * @param $node
     * @return array|null
     */
    public function getSimpleForm($node)
    {
        $classNameing = $this->getNativeClassnamimg($node);

        return (isset($this->contentNodeSimpleForm[$classNameing['name']])) ? $this->contentNodeSimpleForm[$classNameing['name']] : null;
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
        $classNameing = $this->getNativeClassnamimg($node);

        return (isset($this->contentNodeTemplates[$classNameing['name']])) ? $this->contentNodeTemplates[$classNameing['name']] : null;
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
        //check if the node is already related to a template
        $template = $node->getTemplate();

        // if no template is set try to guess one
        if (!$template) {

            $refClass = $this->getNativeClassnamimg($node);

            $className = $refClass['name'];
            $namespace = $refClass['namespace'];

            if (!empty($this->contentNodeTemplates[$className]) && count($this->contentNodeTemplates[$className]) > 0)
                $template = reset($this->contentNodeTemplates[$className]);
            else {

                $bundleName = $this->getBundleNameFromEntity($namespace, $this->get('kernel')->getBundles());

                $template = $this->getDefaultTemplate($className, $bundleName);
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
    public function getDefaultTemplate($className, $bundleName = "MMCmfContentBundle")
    {
        return $bundleName . ':cmf:' . $className . '/' . $className . '_default.html.twig';
    }

}

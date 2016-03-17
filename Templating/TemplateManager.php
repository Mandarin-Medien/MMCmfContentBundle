<?php

namespace MandarinMedien\MMCmfContentBundle\Templating;

use MandarinMedien\MMCmfContentBundle\Entity\TemplatableNodeInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class TemplateManager
 *
 * handle the templates of TemplatableNodeInterface entities
 *
 * @package MandarinMedien\MMCmfContentBundle\Templating
 */
class TemplateManager
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $templates;


    /**
     * TemplateManager constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
        $this->templates = array();
    }


    /**
     * register template
     *
     * @param string $class fully qualified class-name
     * @param string $name name of the template
     * @param string $template template path
     * @return $this
     */
    public function registerTemplate($class, $name, $template)
    {
        $this->templates[$class][$name] = $template;
        return $this;
    }


    /**
     * get a list of all templates assigned to the given class
     *
     * @param string $class
     * @return mixed
     */
    public function getTemplates($class)
    {
        if (isset($this->templates[$class])) {
            return $this->templates[$class];
        } else {
            return array();
        }
    }


    /**
     * get the assigned template of the given TemplatableNodeInterface
     * handles the template selection if no template is assigned
     *
     * @param TemplatableNodeInterface $node
     * @return mixed|string
     */
    public function getTemplate(TemplatableNodeInterface $node)
    {

        $template = $node->getTemplate();

        if (!$template) {

            $meta = $this->manager->getClassMetadata(get_class($node));

            if (!empty($this->templates[$meta->name]) && count($this->templates[$meta->name]) > 0)
                $template = reset($this->templates[$meta->name]);

            else {
                $name = str_replace($meta->namespace, '', $meta->name);
                $bundleName = $this->getBundleNameFromEntity($meta->namespace, $this->container->get('kernel')->getBundles());
                $template = $this->getDefaultTemplate($name, $bundleName);
            }
        }

        return $template;

    }

    /**
     * Get the bundle name from an Entity namespace
     *
     * @param $entityNamespace
     * @param $bundles
     * @return string|null
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

        return null;
    }


    /**
     * get the default template path if no templates are configured
     *
     * @param string $bundleName
     * @return string
     */
    protected function getDefaultTemplate($className, $bundleName = "MMCmfContentBundle")
    {
        return $bundleName . ':cmf:' . $className . '/' . $className . '_default.html.twig';
    }

}
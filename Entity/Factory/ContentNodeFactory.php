<?php

namespace MandarinMedien\MMCmfContentBundle\Entity\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;


class ContentNodeFactory
{

    private $manager;
    private $factory_class = ContentNode::class;
    private $meta;


    /**
     * ContentNodeFactory constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->meta = $this->manager->getClassMetadata(
            $this->factory_class
        );
    }


    /**
     * create a new ContentNode instance by discriminator value
     * @param string $discriminator
     * @return ContentNode
     * @throws \Exception
     */
    public function createContentNode($discriminator = 'default')
    {
        $reflection = new \ReflectionClass($this->getClassByDiscriminator($discriminator));
        return $reflection->newInstance();
    }


    /**
     * get all available discriminator values of ContentNode entity
     * @param array $exclude exclude specific discriminators
     * @return array
     */
    public function getDiscriminators($exclude = array())
    {

        // prefilter discriminators by subclasses
        $subclasses = $this->meta->subClasses;

        $discriminators = array_filter(
            $this->meta->discriminatorMap,
            function($class) use ($subclasses)
            {
                return in_array($class, $subclasses);
            }
        );


        // filter discriminators by exlude array
        return array_diff(array_keys($discriminators), $exclude);
    }


    /**
     * get the discriminator value by the given instance
     * @param ContentNode $contentNode
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getDiscriminatorByClass(ContentNode $contentNode)
    {
        return $this->manager->getClassMetadata(get_class($contentNode))->discriminatorValue;
    }

    /**
     * get the discriminator value by the given className
     * @param string $contentNodeClassName
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getDiscriminatorByClassName($contentNodeClassName)
    {
        return $this->manager->getClassMetadata($contentNodeClassName)->discriminatorValue;
    }


    /**
     * get the ContentNode subclass by discriminator value
     * @param string $discriminator
     * @return string
     * @throws \Exception
     */
    public function getClassByDiscriminator($discriminator)
    {
        if ($class = ($this->meta->discriminatorMap[$discriminator])) {
            return $class;
        } else {
            throw new \Exception('class not found');
        }
    }
}
<?php

namespace MandarinMedien\MMCmfContentBundle\Entity\Factory;

use Doctrine\ORM\EntityManager;
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
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->meta = $this->manager->getClassMetadata(
            $this->factory_class
        );
    }


    /**
     * create a new ContentNode instance by discrimator value
     * @param string $discrimator
     * @return ContentNode
     * @throws \Exception
     */
    public function createContentNode($discrimator = 'default')
    {
        $reflection = new \ReflectionClass($this->getClassByDiscrimator($discrimator));
        return $reflection->newInstance();
    }


    /**
     * get all available discrimator values of ContentNode entity
     * @param array $exclude exclude specific discrimators
     * @return array
     */
    public function getDiscrimators($exclude = array())
    {
        return array_diff(array_keys($this->meta->discriminatorMap), $exclude);
    }


    /**
     * get the discrimator value by the given instance
     * @param ContentNode $contentNode
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getDiscrimatorByClass(ContentNode $contentNode)
    {
        return $this->manager->getClassMetadata(get_class($contentNode))->discriminatorValue;
    }


    /**
     * get the ContentNode subclass by discrimator value
     * @param string $discrimator
     * @return ContentNodeInterface
     * @throws \Exception
     */
    protected function getClassByDiscrimator($discrimator)
    {
        if ($class = ($this->meta->discriminatorMap[$discrimator])) {
            return $class;
        } else {
            throw new \Exception('class not found');
        }
    }
}
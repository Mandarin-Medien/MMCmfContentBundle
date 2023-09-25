<?php

namespace MandarinMedien\MMCmfContentBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\Enti;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    public function __construct(EntityManagerInterface $objectManager, $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
    }

    public function transform($entity)
    {

        if(is_null($entity)) return null;

        return $entity->getId();
    }

    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->objectManager
            ->getRepository($this->class)
            ->find($id);


        if (null === $entity) {
            throw new TransformationFailedException();
        }

        return $entity;
    }
}
<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FormTypeMetaReader
{


    /**
     * @var PsrCachedReader
     */
    protected $reader;


    /**
     * FormTypeMetaReader constructor.
     * @param Reader|null $reader
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader ?: new PsrCachedReader(
            new AnnotationReader(),
            new FilesystemAdapter()
        );
    }

    /**
     * @param $class
     * @param $property
     * @return null
     * @throws \ReflectionException
     */
    public function get($class, $property)
    {

        $property   = new \ReflectionProperty($class, $property);

        $annotation = $this->reader->getPropertyAnnotation($property, FormTypeMeta::class);

        if($annotation) {
            return $annotation->getValue();
        }

        return null;
    }


    /**
     * @param $class
     * @param $property
     * @return null
     * @throws \ReflectionException
     */
    public function getFormType($class, $property)
    {
        return $this->get($class, $property);
    }


    /**
     * @param $class
     * @param $property
     * @return array
     * @throws \ReflectionException
     */
    public function getOptions($class, $property)
    {
        $property   = new \ReflectionProperty($class, $property);

        $annotation = $this->reader->getPropertyAnnotation($property, FormTypeMeta::class);

        if($annotation) {
            return $annotation->getOptions();
        } else {
            return [];
        }
    }

    /**
     * @param $class
     * @param $property
     * @return null|object
     * @throws \ReflectionException
     */
    public function getFormTypeMeta($class, $property)
    {
        $property   = new \ReflectionProperty($class, $property);

        return $this->reader->getPropertyAnnotation($property, FormTypeMeta::class);
    }
}
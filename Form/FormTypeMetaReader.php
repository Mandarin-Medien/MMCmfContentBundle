<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

use Doctrine\Common\Annotations\AnnotationReader;

class FormTypeMetaReader
{

    /**
     * @param $class
     * @param $property
     * @return null
     */
    public function get($class, $property)
    {

        $reader     = new AnnotationReader();
        $property   = new \ReflectionProperty($class, $property);

        $annotation = $reader->getPropertyAnnotation($property, FormTypeMeta::class);

        if($annotation) {
            return $annotation->getValue();
        }

        return null;
    }

    public function getFormType($class, $property)
    {
        return $this->get($class, $property);
    }

    public function getOptions($class, $property)
    {
        $reader     = new AnnotationReader();
        $property   = new \ReflectionProperty($class, $property);

        $annotation = $reader->getPropertyAnnotation($property, FormTypeMeta::class);

        if($annotation) {
            return $annotation->getOptions();
        } else {
            return [];
        }
    }

    /**
     * @return FormTypeMeta
     */
    public function getFormTypeMeta($class, $property)
    {
        $reader     = new AnnotationReader();
        $property   = new \ReflectionProperty($class, $property);

        return $reader->getPropertyAnnotation($property, FormTypeMeta::class);
    }
}
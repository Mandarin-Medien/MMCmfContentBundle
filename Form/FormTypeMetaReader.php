<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

use Doctrine\Common\Annotations\AnnotationReader;

class FormTypeMetaReader
{
    public function get($class, $property)
    {

        $reader     = new AnnotationReader();
        $property   = new \ReflectionProperty($class, $property);

        $annotation = $reader->getPropertyAnnotation($property, FormTypeMeta::class);

        //var_dump($annotation->getValue());

        dump($annotation);

        if($annotation) {
            return $annotation->getValue();
        }

        return null;
    }
}
<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

use Doctrine\Common\Annotations\Annotation;


/**
 * Class FormTypeAnnotation
 * @package MandarinMedien\MMCmfContentBundle\Form
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class FormTypeMeta extends Annotation
{

    public function getValue()
    {
        return $this->value;
    }
}
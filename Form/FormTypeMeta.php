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


    /**
     * @var string className of FormType
     */
    public $class;


    /**
     * @var array
     */
    public $options = [];


    public function getValue()
    {
        if($this->value) {
            return $this->value;
        } else {
            return $this->class;
        }
    }


    /**
     * @return string
     */
    public function getClass()
    {
        if($this->class) {
            return $this->class;
        } else {
            return $this->value;
        }
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
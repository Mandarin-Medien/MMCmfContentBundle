<?php

namespace MandarinMedien\MMCmfContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HTMLType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => array(
                'data-form-type' => 'html',
            )
        ));
    }

    public function getParent()
    {
        return TextAreaType::class;
    }

}
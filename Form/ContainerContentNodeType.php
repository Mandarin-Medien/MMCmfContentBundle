<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

use MandarinMedien\MMCmfContentBundle\Form\Type\NodeTreeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContainerContentNodeType extends ContentNodeType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')

            ->add('visible')
            ->add('classes')
            ->add('fluid')
            ->add('parent', NodeTreeType::class, array('parentNode' => $options['root_node']))
            ->add('position')
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefined(array('data_class'))
            ->setDefault('data_class', 'MandarinMedien\MMCmfContentBundle\Entity\ContainerContentNode');
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mandarinmedien_mmcmfcontentbundle_containercontentnode';
    }
}

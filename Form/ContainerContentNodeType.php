<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

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
            ->add('classes')
            ->add('fluid')
            ->add('parent', $this->container->get('mm_cmf_content.form_type.node_tree')->setParentNode($options['root_node']));
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

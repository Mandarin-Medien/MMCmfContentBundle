<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use MandarinMedien\MMCmfNodeBundle\Entity\Node;


class ContentNodeType extends AbstractType
{

    protected $container;
    protected $hiddenFields = array('id');


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function addHiddenField($fieldName)
    {
        $this->hiddenFields[] = $fieldName;
    }

    public function removeHiddenField($fieldName)
    {
        if(($key = array_search($fieldName, $this->hiddenFields)) !== false) {
            unset($this->hiddenFields[$key]);
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $em = $this->container->get('doctrine')->getManager();
        $className  = get_class($options['data']);

        $metaData = $em->getClassMetadata($className);
        $formTypeReader = new FormTypeMetaReader();

        // loop default fields
        foreach($metaData->getFieldNames() as $field)
        {
            if(in_array($field, $this->hiddenFields)) continue;


            $builder->add($field, $formTypeReader->get($className, $field));

        }

        // loop association fields
        foreach($metaData->getAssociationNames() as $field)
        {

            if(in_array($field, array(
                'parent',
                'nodes',
                'routes',
                'template'
            ))) continue;

            $builder->add($field, $formTypeReader->get($className, $field));
        }

        $builder->add('parent', $this->container->get('mm_cmf_content.form_type.node_tree')->setParentNode($options['root_node']));
        $builder->add('template', $this->container->get('mm_cmf_content.form_type.node_template')->setClass($className));
    }


    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver
            ->setDefined(array('root_node'))
            ->setAllowedTypes('root_node', array(Node::class, 'null'))
            ->setDefault('root_node', null);

    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'mm_cmf_admin_content_node';
    }

}
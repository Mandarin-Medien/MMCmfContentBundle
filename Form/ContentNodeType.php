<?php

namespace MandarinMedien\MMCmfContentBundle\Form;

use MandarinMedien\MMCmfContentBundle\Form\Type\NodeTreeType;
use MandarinMedien\MMCmfContentBundle\Form\Type\TemplatableNodeTemplateType;
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
        if (($key = array_search($fieldName, $this->hiddenFields)) !== false) {
            unset($this->hiddenFields[$key]);
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['hiddenFields'] as $field)
            $this->addHiddenField($field);


        $em = $this->container->get('doctrine')->getManager();
        $className = get_class($options['data']);

        $metaData = $em->getClassMetadata($className);
        $formTypeReader = new FormTypeMetaReader();

        // loop default fields
        foreach ($metaData->getFieldNames() as $field) {
            if (in_array($field, $this->hiddenFields)) continue;


            $builder->add($field, $formTypeReader->get($className, $field));

        }

        // loop association fields
        foreach ($metaData->getAssociationNames() as $field) {

            if (in_array($field, array(
                'parent',
                'nodes',
                'routes',
                'template'
            ))) continue;

            $builder->add($field, $formTypeReader->get($className, $field));
        }

        $builder->add('parent', NodeTreeType::class, array('parentNode' => $options['root_node']));
        $builder->add('template', TemplatableNodeTemplateType::class, array('className' => $className));
    }


    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver
            ->setDefined(
                array(
                    'root_node',
                    'hiddenFields'
                )
            )
            ->setAllowedTypes('root_node', array(Node::class, 'null'))
            ->setDefault('root_node', null)
            ->setDefined(array('root_node'))
            ->setAllowedTypes('hiddenFields', array('array'))
            ->setDefault('hiddenFields', array());
    }


    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mm_cmf_admin_content_node';
    }

}
<?php

namespace MandarinMedien\MMCmfContentBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfContentBundle\Form\DataTransformer\IdToEntityTransformer;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityHiddenType extends AbstractType
{

    protected $objectManager;

    function __construct(EntityManagerInterface $om)
    {
        $this->objectManager = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new IdToEntityTransformer($this->objectManager, $options['class']);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefined(array('class'));
        $resolver->setAllowedTypes('class', array('NULL','string',NodeInterface::class));

        $resolver
            ->setRequired(array('class'))
            ->setDefaults(array(
                'invalid_message' => 'The entity does not exist.',
            ));
    }


    public function getParent()
    {
        return HiddenType::class;
    }

    public function getBlockPrefix()
    {
        return 'entity_hidden';
    }

}
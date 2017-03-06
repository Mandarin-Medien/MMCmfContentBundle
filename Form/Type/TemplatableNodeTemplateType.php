<?php

namespace MandarinMedien\MMCmfContentBundle\Form\Type;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplatableNodeTemplateType extends AbstractType
{
    private $container;
    private $class;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefined(array('className'));
        $resolver->setAllowedTypes('className', array('string',NodeInterface::class));

        /**
         * @var $tempThis TemplatableNodeTemplateType
         */
        $tempThis = $this;


        $resolver->setDefaults(array(
            'choices' => function (Options $options) use ($tempThis, $resolver) {

                $templates = array();
                if ($options['className']) {
                    $templates = $tempThis->getTemplates($options['className']);

                    // null detault template to avoid unnessary database entry
                    $templates['default'] = "";

                    //$templates = array_flip($templates);

                }

                return $templates;


            },
            'required' => false,
            'placeholder' => false,
        ));
    }

    /**
     * define a node as entry point for the node tree
     *
     * @param ContentNode|string $node
     * @return array|null
     */
    public function getTemplates($node = null)
    {
        /*$contentNodeParser = $this->container->get('mm_cmf_content.content_parser');
        $templates = $contentNodeParser->getTemplates($node);*/

        return $this->container->get('mm_cmf_content.template_manager')->getTemplates($node);

        //return $templates;
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
    }


    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mm_cmf_content_templatable_node_template';
    }


}
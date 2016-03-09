<?php

namespace MandarinMedien\MMCmfContentBundle\Form\Type;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentNodeTemplateType extends AbstractType
{
    private $container;
    private $class;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * define a node as entry point for the node tree
     *
     * @param ContentNode|string $node
     * @return ContentNodeTemplateType
     */
    public function setClass($node = null)
    {
        $this->class = $node;
        return $this;
    }

    /**
     * @return ContentNode|string
     */
    protected function getClass()
    {
        return $this->class;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $templates =$this->getTemplates($this->class);

        // null detault template to avoid unnessary database entry
        $templates['default'] = "";

        $templates = array_flip($templates);

        $resolver->setDefaults(array(
            'choices' => $templates,
            'required' => false,
            'placeholder' => false
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
        $contentNodeParser = $this->container->get('mm_cmf_content.content_parser');
        $templates = $contentNodeParser->getTemplates($node);

        return $templates;
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
    public function getName()
    {
        return 'mm_cmf_content_content_node_template';
    }


}
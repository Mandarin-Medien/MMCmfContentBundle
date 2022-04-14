<?php

namespace MandarinMedien\MMCmfContentBundle\Form\Type;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;


class NodeTreeType extends AbstractType
{
    protected $class;
    protected $manager;
    protected $parentNode = null;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;

        $this->class = Node::class;
    }

    /**
     * define a node as entry point for the node tree
     *
     * @param NodeInterface $node
     * @return NodeTreeType
     */
    public function setParentNode(NodeInterface $node = null)
    {
        $this->parentNode = $node;
        return $this;
    }

    /**
     * @return NodeInterface|null
     */
    protected function getParentNode()
    {
        return $this->parentNode;
    }


    /**
     * @return Node
     */
    protected function getRootNodes()
    {
        return $this->manager->getRepository($this->class)->findBy(array('parent'=>null));
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $this->setParentNode($options['parentNode']);

        $view->vars['nodes'] = $this->getParentNode() ? array($this->getParentNode()) : $this->getRootNodes();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefined(array('parentNode'));
        $resolver->setAllowedTypes('parentNode', array('NULL','string',NodeInterface::class));

        $resolver->setDefaults(array(
            'class' => $this->class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mm_cmf_content_node_tree';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return EntityHiddenType::class;
    }
}
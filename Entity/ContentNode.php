<?php

namespace MandarinMedien\MMCmfContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfContentBundle\FormTypeMeta as FormType;

/**
 * ContentNode
 */
class ContentNode extends Node implements TemplatableNodeInterface
{

    /**
     * @var string
     */
    protected $classes;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var string
     */
    protected $template;


    /**
     * @return string
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param string $classes
     * @return ContentNode
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return ContentNode
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return ContentNode
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param ArrayCollection $nodes
     *
     * @return Node
     */
    public function setNodes(Collection $nodes)
    {
        $finalNodes = array();

        foreach ($nodes as $node) {
            if ($this->checkChildNode($node)) {
                $node->setParent($this);
                $finalNodes[] = $node;
            }
        }

        $this->nodes = $finalNodes;

        return $this;
    }


    /**
     * @param NodeInterface $node
     *
     * @return $this
     */
    public function addNode(NodeInterface $node)
    {
        if ($this->checkChildNode($node)) {
            $this->nodes->add($node);

            $node->setPosition(count($this->getNodes()));
            $node->setParent($this);
        }

        return $this;
    }

    /**
     * Function to check if the child node is valid
     * will be called by this::addNode and this::setNodes
     * > please overwrite
     *
     * @param NodeInterface $node
     *
     * @return bool
     */
    protected function checkChildNode(NodeInterface $node)
    {
        return true;
    }
}


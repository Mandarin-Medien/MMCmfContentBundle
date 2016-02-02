<?php

namespace MandarinMedien\MMCmfContentBundle\Entity;

/**
 * ContentNode
 */
class ContentNode extends \MandarinMedien\MMCmfNodeBundle\Entity\Node
{

    /**
     * @var integer
     */
    private $position;

    /**
     * @var string
     */
    private $classes;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var string
     */
    private $template;

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return ContentNode
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

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
}


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
        return $this->template?:$this->getDefaultTemplate();
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

    public function getDefaultTemplate()
    {
        $class = $this->parse_classname(__CLASS__);

        return 'MMCmfContentBundle:cmf:'.$class['classname'].'/'.$class['classname'] .'_default.html.twig';
    }

    private function parse_classname ($name)
    {
        return array(
            'namespace' => array_slice(explode('\\', $name), 0, -1),
            'classname' => join('', array_slice(explode('\\', $name), -1)),
        );
    }
}


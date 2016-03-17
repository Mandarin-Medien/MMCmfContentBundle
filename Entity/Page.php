<?php

namespace MandarinMedien\MMCmfContentBundle\Entity;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfRoutingBundle\Entity\AutoNodeRoute;

/**
 * Page
 */
class Page extends Node implements TemplatableNodeInterface
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $keywords;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $robots;

    /**
     * @var string
     */
    protected $author;


    /**
     * @var string
     */
    protected $template;


    /**
     * @var boolean
     */
    protected $routeGeneration = true;


    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return Page
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Page
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set robots
     *
     * @param string $robots
     *
     * @return Page
     */
    public function setRobots($robots)
    {
        $this->robots = $robots;

        return $this;
    }

    /**
     * Get robots
     *
     * @return string
     */
    public function getRobots()
    {
        return $this->robots;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Page
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * builds autogenerated route
     *
     * @return \MandarinMedien\MMCmfRoutingBundle\Entity\NodeRoute|null
     */
    public function getAutoNodeRoute()
    {
        foreach($this->getRoutes() as $route)
        {
            if($route instanceof AutoNodeRoute) {
                return $route;
            }
        }

        return null;
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
     * @return Page
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }


    /**
     * to string function
     *
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }
}


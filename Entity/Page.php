<?php

namespace MandarinMedien\MMCmfContentBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfRoutingBundle\Entity\AutoNodeRoute;
use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRouteInterface;
use MandarinMedien\MMCmfRoutingBundle\Entity\RoutableNodeInterface;

/**
 * Page
 */
class Page extends Node implements PageNodeInterface, RoutableNodeInterface, TemplatableNodeInterface
{

    /**
     * @var string
     */
    protected $metaTitle;

    /**
     * @var string
     */
    protected $metaKeywords;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * @var string
     */
    protected $metaRobots;

    /**
     * @var string
     */
    protected $metaAuthor;

    /**
     * @var string
     */
    protected $metaImage;


    /**
     * @var string
     */
    protected $template;


    /**
     * @var boolean
     */
    protected $routeGeneration = true;



    protected $routes;


    public function __construct()
    {
        parent::__construct();
        $this->routes = new ArrayCollection();
    }


    /**
     * @return NodeRoute[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param ArrayCollection $routes
     * @return Node
     */
    public function setRoutes(ArrayCollection $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * @param NodeRouteInterface $route
     * @return $this
     */
    public function addRoute(NodeRouteInterface $route)
    {
        $this->routes->add($route);
        return $this;
    }


    /**
     * @param NodeRouteInterface $route
     * @return $this
     */
    public function removeRoute(NodeRouteInterface $route)
    {
        $this->routes->removeElement($route);
        return $this;
    }


    public function setAutoNodeRouteGeneration($autoNodeRouteGeneration)
    {
        $this->routeGeneration = true;
    }

    public function hasAutoNodeRouteGeneration()
    {
        return $this->routeGeneration;
    }


    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     * @return Page
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     * @return Page
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     * @return Page
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaRobots()
    {
        return $this->metaRobots;
    }

    /**
     * @param string $metaRobots
     * @return Page
     */
    public function setMetaRobots($metaRobots)
    {
        $this->metaRobots = $metaRobots;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaAuthor()
    {
        return $this->metaAuthor;
    }

    /**
     * @param string $metaAuthor
     * @return Page
     */
    public function setMetaAuthor($metaAuthor)
    {
        $this->metaAuthor = $metaAuthor;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaImage()
    {
        return $this->metaImage;
    }

    /**
     * @param string $metaImage
     * @return Page
     */
    public function setMetaImage($metaImage)
    {
        $this->metaImage = $metaImage;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRouteGeneration()
    {
        return $this->routeGeneration;
    }

    /**
     * @param boolean $routeGeneration
     * @return Page
     */
    public function setRouteGeneration($routeGeneration)
    {
        $this->routeGeneration = $routeGeneration;
        return $this;
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


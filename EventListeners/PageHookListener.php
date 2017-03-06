<?php

namespace MandarinMedien\MMCmfContentBundle\EventListeners;

use Doctrine\Common\EventArgs;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostLoad;
use MandarinMedien\MMCmfContentBundle\Entity\Page;
use Symfony\Component\DependencyInjection\Container;


/**
 * Class PageHookListener
 *
 * Entity Liste for Page entity, needed for PageHook support
 *
 * @package MandarinMedien\MMCmfContentBundle\EventListeners
 */
class PageHookListener
{
    /**
     * @var Container
     */
    protected $container;


    /**
     * PageHookListener constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * postLoad Callback for Page entity, gets all defined PageHooks and calls it
     * @param Page $page
     * @param LifecycleEventArgs $args
     */
    public function postLoad(Page $page, LifecycleEventArgs $args)
    {
        $hookManager = $this->container->get('mm_cmf_content.page_hook_manager');

        foreach($hookManager->getHooks() as $hook) {
            $hook->process($page, $this->container->get('request_stack')->getCurrentRequest());
        };
    }

}
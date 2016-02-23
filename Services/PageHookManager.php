<?php

namespace MandarinMedien\MMCmfContentBundle\Services;

/**
 * Class PageHookManager
 * @package MandarinMedien\MMCmfContentBundle\Services
 */
class PageHookManager
{

    /**
     * @var array
     */
    private $hooks = array();


    /**
     * add PageHook for Page postLoad callback chain
     * @param PageHookInterface $hook
     */
    public function addHook(PageHookInterface $hook)
    {
      $this->hooks[] = $hook;
    }


    /**
     * get all defined PageHooks
     * @return PageHookInterface[]
     */
    public function getHooks()
    {
        return $this->hooks;
    }

}
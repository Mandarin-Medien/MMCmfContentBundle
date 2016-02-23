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
     * @param PageHook $hook
     */
    public function addHook(PageHook $hook)
    {
      $this->hooks[] = $hook;
    }


    /**
     * get all defined PageHooks
     * @return PageHook[]
     */
    public function getHooks()
    {
        return $this->hooks;
    }

}
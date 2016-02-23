<?php

namespace MandarinMedien\MMCmfContentBundle\Services;

/**
 * Class PageHook
 * @package MandarinMedien\MMCmfContentBundle\Services
 */
class PageHook
{

    /**
     * @var callable
     */
    protected $hook;


    /**
     * define callable called on Page postLoad Event
     * @param callable $callable function to be called
     * @return PageHook
     */
    public function setHook(callable $callable)
    {
        $this->hook = $callable;
        return $this;
    }


    /**
     * call the defined hook and pass trhu all given argumenets
     */
    public function call()
    {
        call_user_func_array($this->hook, func_get_args());
    }
}
<?php

namespace MandarinMedien\MMCmfContentBundle\Services;
use MandarinMedien\MMCmfContentBundle\Entity\Page;

/**
 * Class PageHook
 * @package MandarinMedien\MMCmfContentBundle\Services
 */
interface PageHookInterface
{
    /**
     * process the hook
     * @param Page $page Page Entity given by the postLoad event
     */
    public function process(Page $page);
}
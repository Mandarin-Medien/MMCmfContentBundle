<?php

namespace MandarinMedien\MMCmfContentBundle\Services;
use MandarinMedien\MMCmfContentBundle\Entity\Page;
use Symfony\Component\HTTPFoundation\Request;

/**
 * Class PageHook
 * @package MandarinMedien\MMCmfContentBundle\Services
 */
interface PageHookInterface
{
    /**
     * process the hook
     * @param Page $page Page Entity given by the postLoad event
     * @param Request $request
     */
    public function process(Page $page, Request $request);
}
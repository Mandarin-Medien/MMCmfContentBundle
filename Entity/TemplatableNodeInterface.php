<?php

namespace MandarinMedien\MMCmfContentBundle\Entity;

interface TemplatableNodeInterface
{

    public function setTemplate($template);

    public function getTemplate();

}
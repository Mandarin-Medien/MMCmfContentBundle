<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContentParserController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MMCmfContentBundle:Default:index.html.twig', array('name' => $name));
    }

    public function parse($nodes){

    }
}

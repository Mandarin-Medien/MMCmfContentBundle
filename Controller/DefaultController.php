<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\RowContentNode;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $node = new RowContentNode();
        $node->setName("Parent Node");
        $node->setAttributes(Array('data-custom-data'=>'foobar'));

        $node1 = new ContentNode();
        $node1->setName("Child Node 1");
        $node->addNode($node1);

        $node2 = new ContentNode();
        $node2->setName("Child Node 2");
        $node->addNode($node2);

        $node3 = new ContentNode();
        $node3->setName("Child Node 3");
        $node->addNode($node3);

        return $this->render('MMCmfContentBundle:Default:index.html.twig',
            array(
                'node' => $node
            )
        );



    }
}

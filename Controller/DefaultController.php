<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;

use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\RowContentNode;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function indexAction()
    {

        $nodeRow = new RowContentNode();
        $nodeRow->setName("Parent Node");
        $nodeRow->setAttributes(Array('data-custom-data'=>'foobar'));


/*        $nodeR1 = new RowContentNode();
        $nodeR1->setName("Parent Node");
        $nodeRow->addNode($nodeR1);*/

        $node1 = new ContentNode();
        $node1->setName("Child Node 1");
        $nodeRow->addNode($node1);

        $node2 = new ContentNode();
        $node2->setName("Child Node 2");
        $nodeRow->addNode($node2);

        $node3 = new ContentNode();
        $node3->setName("Child Node 3");
        $nodeRow->addNode($node3);

        return $this->render('@MMCmfContent/Default/index.html.twig',
            array(
                'node' => $nodeRow
            )
        );

    }
}

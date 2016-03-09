<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 03.02.16
 * Time: 16:56
 */

namespace MandarinMedien\MMCmfContentBundle\Entity;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;

class RowContentNode extends ContentNode
{
    protected $draggable = false;

    /**
     * @return boolean
     */
    public function isDraggable()
    {
        return $this->draggable;
    }

    /**
     * @param boolean $draggable
     * @return RowContentNode
     */
    public function setDraggable($draggable)
    {
        $this->draggable = $draggable;
        return $this;
    }



    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    protected function checkChildNode(NodeInterface $node)
    {
        if($node instanceof RowContentNode)
        {
            trigger_error('You cant append a RowContentNode underneath another RowContentNode ', E_USER_WARNING);
            return false;
        }

        return true;
    }

}
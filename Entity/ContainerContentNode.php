<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 03.02.16
 * Time: 16:56
 */

namespace MandarinMedien\MMCmfContentBundle\Entity;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;

class ContainerContentNode extends ContentNode
{
    protected $fluid = false;

    /**
     * @return boolean
     */
    public function isFluid()
    {
        return $this->fluid;
    }

    /**
     * @param boolean $fluid
     * @return ContainerContentNode
     */
    public function setFluid($fluid)
    {
        $this->fluid = $fluid;
        return $this;
    }


    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    protected function checkChildNode(NodeInterface $node)
    {
        if($node instanceof ContainerContentNode)
        {
            trigger_error('You can not append a ContainerContentNode underneath another ContainerContentNode ', E_USER_WARNING);
            return false;
        }

        return true;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 03.02.16
 * Time: 16:56
 */

namespace MandarinMedien\MMCmfContentBundle\Entity;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;

class ParagraphContentNode extends ContentNode
{
    protected $headline;
    protected $text;
    protected $headlineType;
    protected $imageSource;

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    protected function checkChildNode(NodeInterface $node)
    {
        if ($node instanceof RowContentNode) {
            return true;
        } else {
            trigger_error('You have to append an entity of Class RowContentNode', E_USER_WARNING);
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param mixed $headline
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getHeadlineType()
    {
        return $this->headlineType;
    }

    /**
     * @param mixed $headlineType
     */
    public function setHeadlineType($headlineType)
    {
        $this->headlineType = $headlineType;
    }

    /**
     * @return mixed
     */
    public function getImageSource()
    {
        return $this->imageSource;
    }

    /**
     * @param mixed $imageSource
     */
    public function setImageSource($imageSource)
    {
        $this->imageSource = $imageSource;
    }



}
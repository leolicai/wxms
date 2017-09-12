<?php
/**
 * Tag.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/12
 * Version: 1.0
 */

namespace Weixin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Tag
 * @package Weixin\Entity
 * @ORM\Entity(repositoryClass="\Weixin\Repository\TagRepository")
 * @ORM\Table(name="wx_tag")
 */
class Tag
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, options={"fixed" = true})
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $tagID;

    /**
     * @var string
     * @ORM\Column(name="tag_name", type="string", length=30)
     */
    private $tagName = '';

    /**
     * @var integer
     * @ORM\Column(name="tag_count", type="integer")
     */
    private $tagCount = 0;

    /**
     * @var Weixin
     * @ORM\ManyToOne(targetEntity="Weixin\Entity\Weixin", inversedBy="wxTags")
     * @ORM\JoinColumn(name="wx_id", referencedColumnName="wx_id")
     */
    private $tagWeixin;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getTagID()
    {
        return $this->tagID;
    }

    /**
     * @param int $tagID
     */
    public function setTagID($tagID)
    {
        $this->tagID = $tagID;
    }

    /**
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @param string $tagName
     */
    public function setTagName($tagName)
    {
        $this->tagName = $tagName;
    }

    /**
     * @return int
     */
    public function getTagCount()
    {
        return $this->tagCount;
    }

    /**
     * @param int $tagCount
     */
    public function setTagCount($tagCount)
    {
        $this->tagCount = $tagCount;
    }

    /**
     * @return Weixin
     */
    public function getTagWeixin()
    {
        return $this->tagWeixin;
    }

    /**
     * @param Weixin $tagWeixin
     */
    public function setTagWeixin($tagWeixin)
    {
        $this->tagWeixin = $tagWeixin;
    }



}
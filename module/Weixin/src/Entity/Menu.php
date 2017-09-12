<?php
/**
 * Menu.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/12
 * Version: 1.0
 */

namespace Weixin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Menu
 * @package Weixin\Entity
 *
 * @ORM\Entity(repositoryClass="\Weixin\Repository\MenuRepository")
 * @ORM\Table(name="wx_menu")
 */
class Menu
{

    const TYPE_DEFAULT = 1;
    const TYPE_ADDITIONAL = 2;

    const STATUS_ACTIVATED = 1;
    const STATUS_BACKUP = 0;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, options={"fixed" = true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="menu_name", type="string", length=45)
     */
    private $menuName = '';

    /**
     * @var string
     * @ORM\Column(name="menu_id", type="string", length=45)
     */
    private $menuID = '';

    /**
     * @var string
     * @ORM\Column(name="menu_data", type="text", length=65535)
     */
    private $menuData = '';

    /**
     * @var integer
     * @ORM\Column(name="menu_type", type="integer")
     */
    private $menuType = self::TYPE_ADDITIONAL;

    /**
     * @var integer
     * @ORM\Column(name="menu_status", type="integer")
     */
    private $menuStatus = self::STATUS_BACKUP;

    /**
     * @var \DateTime
     * @ORM\Column(name="menu_created", type="datetime")
     */
    private $menuCreated;

    /**
     * @var Weixin
     * @ORM\ManyToOne(targetEntity="Weixin\Entity\Weixin", inversedBy="wxMenus")
     * @ORM\JoinColumn(name="wx_id", referencedColumnName="wx_id")
     */
    private $menuWeixin;


    public static function StatusStringList()
    {
        return [
            self::STATUS_ACTIVATED => '使用中',
            self::STATUS_BACKUP => '待启用',
        ];
    }

    public static function TypeStringList()
    {
        return [
            self::TYPE_DEFAULT => '默认类型菜单',
            self::TYPE_ADDITIONAL => '个性化类型菜单',
        ];
    }

    public function getStatusAsString()
    {
        $list = self::StatusStringList();
        return isset($list[$this->menuStatus]) ? $list[$this->menuStatus] : '未知状态';
    }

    public function getTypeAsString()
    {
        $list = self::TypeStringList();
        return isset($list[$this->menuType]) ? $list[$this->menuType] : '未知类型';
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->menuID;
    }

    /**
     * @param string $id
     */
    public function setID($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     * @param string $menuName
     */
    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;
    }

    /**
     * @return string
     */
    public function getMenuID()
    {
        return $this->menuID;
    }

    /**
     * @param string $menuID
     */
    public function setMenuID($menuID)
    {
        $this->menuID = $menuID;
    }

    /**
     * @return string
     */
    public function getMenuData()
    {
        return $this->menuData;
    }

    /**
     * @param string $menuData
     */
    public function setMenuData($menuData)
    {
        $this->menuData = $menuData;
    }

    /**
     * @return int
     */
    public function getMenuType()
    {
        return $this->menuType;
    }

    /**
     * @param int $menuType
     */
    public function setMenuType($menuType)
    {
        $this->menuType = $menuType;
    }

    /**
     * @return int
     */
    public function getMenuStatus()
    {
        return $this->menuStatus;
    }

    /**
     * @param int $menuStatus
     */
    public function setMenuStatus($menuStatus)
    {
        $this->menuStatus = $menuStatus;
    }

    /**
     * @return \DateTime
     */
    public function getMenuCreated()
    {
        return $this->menuCreated;
    }

    /**
     * @param \DateTime $menuCreated
     */
    public function setMenuCreated($menuCreated)
    {
        $this->menuCreated = $menuCreated;
    }

    /**
     * @return Weixin
     */
    public function getMenuWeixin()
    {
        return $this->menuWeixin;
    }

    /**
     * @param Weixin $menuWeixin
     */
    public function setMenuWeixin($menuWeixin)
    {
        $this->menuWeixin = $menuWeixin;
    }



}
<?php
/**
 * Adminer.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Adminer
 *
 * 系统管理员实体定义
 *
 * @package Admin\Entity
 *
 * @ORM\Entity(repositoryClass="\Admin\Repository\AdminerRepository")
 * @ORM\Table(
 *     name="sys_admin",
 *     indexes={
 *         @ORM\Index(name="admin_default_idx", columns={"admin_default"}),
 *         @ORM\Index(name="admin_status_idx", columns={"admin_status"}),
 *         @ORM\Index(name="admin_locked_idx", columns={"admin_locked"}),
 *         @ORM\Index(name="admin_activated_idx", columns={"admin_activated"}),
 *         @ORM\Index(name="admin_level_idx", columns={"admin_level"}),
 *         @ORM\Index(name="admin_active_code_idx", columns={"admin_active_code"})
 *     }
 * )
 */
class Adminer
{
    const DEFAULT_ADMIN = 1;
    const DEFAULT_OTHER = 0;

    const STATUS_VALID = 1;
    const STATUS_INVALID = 0;

    const LOCKED_VALID = 1;
    const LOCKED_INVALID = 0;

    const ACTIVATED_VALID = 1;
    const ACTIVATED_INVALID = 0;

    const LEVEL_SUPERIOR = 999;
    const LEVEL_SENIOR = 30;
    const LEVEL_JUNIOR = 20;
    const LEVEL_INTERIOR = 10;
    const LEVEL_DEFAULT = 1;

    /**
     * 管理员编号
     *
     * @var string
     * @ORM\Id
     * @ORM\Column(name="admin_id", type="string", length=36, options={"fixed" = true})
     */
    protected $adminID;

    /**
     * 管理员登录邮箱
     *
     * @var string
     * @ORM\Column(name="admin_email", type="string", length=45, options={"fixed" = true})
     */
    protected $adminEmail;

    /**
     * 管理员登录密码
     *
     * @var string
     * @ORM\Column(name="admin_passwd", type="string", length=32, options={"fixed" = true})
     */
    protected $adminPasswd;

    /**
     * 默认管理员身份
     *
     * @var integer
     * @ORM\Column(name="admin_default", type="integer")
     */
    protected $adminDefault = self::DEFAULT_OTHER;

    /**
     * 管理员账户状态
     *
     * @var integer
     * @ORM\Column(name="admin_status", type="integer")
     */
    protected $adminStatus = self::STATUS_VALID;

    /**
     * 管理员账户锁定状况
     *
     * @var integer
     * @ORM\Column(name="admin_locked", type="integer")
     */
    protected $adminLocked = self::LOCKED_VALID;

    /**
     * 管理员账户是否被激活
     *
     * @var integer
     * @ORM\Column(name="admin_activated", type="integer")
     */
    protected $adminActivated = self::ACTIVATED_INVALID;

    /**
     * 管理员等级
     *
     * @var integer
     * @ORM\Column(name="admin_level", type="integer")
     */
    protected $adminLevel = self::LEVEL_DEFAULT;

    /**
     * 管理员名字
     *
     * @var string
     * @ORM\Column(name="admin_name", type="string", length=45)
     */
    protected $adminName = '';

    /**
     * 管理员账户激活 CODE
     *
     * @var string
     * @ORM\Column(name="admin_active_code", type="string", length=32, options={"fixed" = true})
     */
    protected $adminActiveCode = '';

    /**
     * 管理员账户失效时间
     *
     * @var \DateTime
     * @ORM\Column(name="admin_expired", type="datetime")
     */
    protected $adminExpired;

    /**
     * 管理员创建时间
     *
     * @var \DateTime
     * @ORM\Column(name="admin_created", type="datetime")
     */
    protected $adminCreated;


    /**
     * @return array
     */
    public static function StatusList()
    {
        return [
            self::STATUS_VALID => '有效',
            self::STATUS_INVALID => '废弃',
        ];
    }

    /**
     * @return array
     */
    public static function LevelList()
    {
        return [
            self::LEVEL_SUPERIOR => '超级管理员',
            self::LEVEL_SENIOR => '高级管理员',
            self::LEVEL_JUNIOR => '中级管理员',
            self::LEVEL_INTERIOR => '初级管理员',
            self::LEVEL_DEFAULT => '管理员',
        ];
    }

    /**
     * @return array
     */
    public static function ActivatedList()
    {
        return [
            self::ACTIVATED_VALID => '已激活',
            self::ACTIVATED_INVALID => '未激活',
        ];
    }

    /**
     * @return array
     */
    public static function LockedList()
    {
        return [
            self::LOCKED_VALID => '被锁定',
            self::LOCKED_INVALID => '未锁定',
        ];
    }

    /**
     * @return string
     */
    public function getAdminStatusAsString()
    {
        $list = self::StatusList();
        return $list[$this->getAdminStatus()];
    }

    /**
     * @return string
     */
    public function getAdminLevelAsString()
    {
        $list = self::LevelList();
        return $list[$this->getAdminLevel()];
    }

    /**
     * @return string
     */
    public function getAdminActivatedAsString()
    {
        $list = self::ActivatedList();
        return $list[$this->getAdminActivated()];
    }

    /**
     * @return string
     */
    public function getAdminLockedAsString()
    {
        $list = self::LockedList();
        return $list[$this->getAdminLocked()];
    }


    /**
     * @return string
     */
    public function getAdminID()
    {
        return $this->adminID;
    }

    /**
     * @param string $adminID
     */
    public function setAdminID($adminID)
    {
        $this->adminID = $adminID;
    }

    /**
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->adminEmail;
    }

    /**
     * @param string $adminEmail
     */
    public function setAdminEmail($adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    /**
     * @return string
     */
    public function getAdminPasswd()
    {
        return $this->adminPasswd;
    }

    /**
     * @param string $adminPasswd
     */
    public function setAdminPasswd($adminPasswd)
    {
        $this->adminPasswd = md5(strtolower($adminPasswd));
    }

    /**
     * @return int
     */
    public function getAdminDefault()
    {
        return $this->adminDefault;
    }

    /**
     * @param int $adminDefault
     */
    public function setAdminDefault($adminDefault)
    {
        $this->adminDefault = $adminDefault;
    }

    /**
     * @return int
     */
    public function getAdminStatus()
    {
        return $this->adminStatus;
    }

    /**
     * @param int $adminStatus
     */
    public function setAdminStatus($adminStatus)
    {
        $this->adminStatus = $adminStatus;
    }

    /**
     * @return int
     */
    public function getAdminLocked()
    {
        return $this->adminLocked;
    }

    /**
     * @param int $adminLocked
     */
    public function setAdminLocked($adminLocked)
    {
        $this->adminLocked = $adminLocked;
    }

    /**
     * @return int
     */
    public function getAdminActivated()
    {
        return $this->adminActivated;
    }

    /**
     * @param int $adminActivated
     */
    public function setAdminActivated($adminActivated)
    {
        $this->adminActivated = $adminActivated;
    }

    /**
     * @return int
     */
    public function getAdminLevel()
    {
        return $this->adminLevel;
    }

    /**
     * @param int $adminLevel
     */
    public function setAdminLevel($adminLevel)
    {
        $this->adminLevel = $adminLevel;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->adminName;
    }

    /**
     * @param string $adminName
     */
    public function setAdminName($adminName)
    {
        $this->adminName = $adminName;
    }

    /**
     * @return string
     */
    public function getAdminActiveCode()
    {
        return $this->adminActiveCode;
    }

    /**
     * @param string $adminActiveCode
     */
    public function setAdminActiveCode($adminActiveCode)
    {
        $this->adminActiveCode = $adminActiveCode;
    }

    /**
     * @return \DateTime
     */
    public function getAdminExpired()
    {
        return $this->adminExpired;
    }

    /**
     * @param \DateTime $adminExpired
     */
    public function setAdminExpired($adminExpired)
    {
        $this->adminExpired = $adminExpired;
    }

    /**
     * @return \DateTime
     */
    public function getAdminCreated()
    {
        return $this->adminCreated;
    }

    /**
     * @param \DateTime $adminCreated
     */
    public function setAdminCreated($adminCreated)
    {
        $this->adminCreated = $adminCreated;
    }

}
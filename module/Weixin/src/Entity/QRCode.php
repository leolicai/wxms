<?php
/**
 * QRCode.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/15
 * Version: 1.0
 */

namespace Weixin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class QRCode
 * @package Weixin\Entity
 *
 * @ORM\Entity(repositoryClass="\Weixin\Repository\QRCodeRepository")
 * @ORM\Table(name="wx_qrcode")
 */
class QRCode
{
    const TYPE_TEMP = 'QR_SCENE';
    const TYPE_TEMP_ID = 'QR_SCENE';
    const TYPE_TEMP_STR = 'QR_STR_SCENE';

    const TYPE_PERSIST = 'QR_LIMIT_STR_SCENE';
    const TYPE_PERSIST_ID = 'QR_LIMIT_SCENE';
    const TYPE_PERSIST_STR = 'QR_LIMIT_STR_SCENE';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="qrcode_id", type="string", length=36, options={"fixed" = true})
     */
    private $qrcodeID;

    /**
     * @var string
     * @ORM\Column(name="qrcode_name", type="string", length=45)
     */
    private $qrcodeName  = '';

    /**
     * @var string
     * @ORM\Column(name="qrcode_type", type="string", length=18, options={"fixed" = true})
     */
    private $qrcodeType = self::TYPE_TEMP;

    /**
     * @var integer
     * @ORM\Column(name="qrcode_expired", type="integer")
     */
    private $qrcodeExpired;

    /**
     * @var string
     * @ORM\Column(name="qrcode_scene", type="string", length=64)
     */
    private $qrcodeScene = '';

    /**
     * @var string
     * @ORM\Column(name="qrcode_wx_url", type="string", length=255)
     */
    private $qrcodeWxUrl = '';

    /**
     * @var \DateTime
     * @ORM\Column(name="qrcode_created", type="datetime")
     */
    private $qrcodeCreated;


    /**
     * @var Weixin
     * @ORM\ManyToOne(targetEntity="Weixin\Entity\Weixin", inversedBy="wxQRCodes")
     * @ORM\JoinColumn(name="wx_id", referencedColumnName="wx_id")
     */
    private $qrcodeWeixin;



    public static function TypeStringList()
    {
        return [
            self::TYPE_TEMP => '临时二维码',
            self::TYPE_PERSIST => '永久二维码',
        ];
    }

    /**
     * @return string
     */
    public function getQrcodeTypeAsString()
    {
        $list = self::TypeStringList();
        return isset($list[$this->qrcodeType]) ? $list[$this->qrcodeType] : '未知类型二维码';
    }

    /**
     * @return string
     */
    public function getQrcodeTypeForMP()
    {
        $scene = $this->getQrcodeScene();

        if ($this->getQrcodeType() == self::TYPE_TEMP) {
            return preg_match("/^[0-9]+$/", $scene) ? self::TYPE_TEMP_ID : self::TYPE_TEMP_STR;
        }
        if (strlen($this->getQrcodeScene()) > 5) {
            return self::TYPE_PERSIST_STR;
        }

        return preg_match("/^[0-9]+$/", $scene) ? self::TYPE_PERSIST_ID : self::TYPE_PERSIST_STR;
    }

    /**
     * @return int|string
     */
    public function getQrcodeSceneForMP()
    {
        $type = $this->getQrcodeTypeForMP();
        $scene = $this->getQrcodeScene();

        if ($type == self::TYPE_TEMP_ID || $type == self::TYPE_PERSIST_ID) {
            $scene = (int)$scene;
            if ($type == self::TYPE_PERSIST_ID) {
                return $scene > 100000 ? 100000 : $scene;
            }
            return $scene;
        }
        if ($type == self::TYPE_TEMP_STR || $type == self::TYPE_PERSIST_STR) {
            return (string)$this->getQrcodeScene();
        }

        return '0';
    }


    /**
     * @return string
     */
    public function getQrcodeID()
    {
        return $this->qrcodeID;
    }

    /**
     * @param string $qrcodeID
     */
    public function setQrcodeID($qrcodeID)
    {
        $this->qrcodeID = $qrcodeID;
    }

    /**
     * @return string
     */
    public function getQrcodeName()
    {
        return $this->qrcodeName;
    }

    /**
     * @param string $qrcodeName
     */
    public function setQrcodeName($qrcodeName)
    {
        $this->qrcodeName = $qrcodeName;
    }

    /**
     * @return string
     */
    public function getQrcodeType()
    {
        return $this->qrcodeType;
    }

    /**
     * @param string $qrcodeType
     */
    public function setQrcodeType($qrcodeType)
    {
        $this->qrcodeType = $qrcodeType;
    }

    /**
     * @return integer
     */
    public function getQrcodeExpired()
    {
        return $this->qrcodeExpired;
    }

    /**
     * @param integer $qrcodeExpired
     */
    public function setQrcodeExpired($qrcodeExpired)
    {
        $this->qrcodeExpired = $qrcodeExpired;
    }

    /**
     * @return string
     */
    public function getQrcodeScene()
    {
        return $this->qrcodeScene;
    }

    /**
     * @param string $qrcodeScene
     */
    public function setQrcodeScene($qrcodeScene)
    {
        $this->qrcodeScene = $qrcodeScene;
    }

    /**
     * @return string
     */
    public function getQrcodeWxUrl()
    {
        return $this->qrcodeWxUrl;
    }

    /**
     * @param string $qrcodeWxUrl
     */
    public function setQrcodeWxUrl($qrcodeWxUrl)
    {
        $this->qrcodeWxUrl = $qrcodeWxUrl;
    }

    /**
     * @return \DateTime
     */
    public function getQrcodeCreated()
    {
        return $this->qrcodeCreated;
    }

    /**
     * @param \DateTime $qrcodeCreated
     */
    public function setQrcodeCreated($qrcodeCreated)
    {
        $this->qrcodeCreated = $qrcodeCreated;
    }

    /**
     * @return Weixin
     */
    public function getQrcodeWeixin()
    {
        return $this->qrcodeWeixin;
    }

    /**
     * @param Weixin $qrcodeWeixin
     */
    public function setQrcodeWeixin($qrcodeWeixin)
    {
        $this->qrcodeWeixin = $qrcodeWeixin;
    }




}
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
    const TYPE_TEMP = 0;
    const TYPE_PERSIST = 1;

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
     * @var integer
     * @ORM\Column(name="qrcode_type", type="integer")
     */
    private $qrcodeType = self::TYPE_TEMP;

    /**
     * @var \DateTime
     * @ORM\Column(name="qrcode_expired", type="datetime")
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
     * @return int
     */
    public function getQrcodeType()
    {
        return $this->qrcodeType;
    }

    /**
     * @param int $qrcodeType
     */
    public function setQrcodeType($qrcodeType)
    {
        $this->qrcodeType = $qrcodeType;
    }

    /**
     * @return \DateTime
     */
    public function getQrcodeExpired()
    {
        return $this->qrcodeExpired;
    }

    /**
     * @param \DateTime $qrcodeExpired
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
<?php
/**
 * Weixin.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Weixin
 * @package Weixin\Entity
 *
 * @ORM\Entity(repositoryClass="\Weixin\Repository\WeixinRepository")
 * @ORM\Table(
 *     name="wx_weixin",
 *     indexes={
 *         @ORM\Index(name="wx_appid_idx", columns={"wx_appid"})
 *     }
 * )
 */
class Weixin
{
    const TRANSFER_PUBLIC = 1;
    const TRANSFER_COMPATIBLE = 2;
    const TRANSFER_ENCODING = 3;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="wx_id", type="integer")
     */
    private $wxID;

    /**
     * @var string
     * @ORM\Column(name="wx_name", type="string", length=45)
     */
    private $wxName = '';

    /**
     * @var string
     * @ORM\Column(name="wx_appid", type="string", length=45, options={"fixed" = true})
     */
    private $wxAppID = '';

    /**
     * @var string
     * @ORM\Column(name="wx_appsecret", type="string", length=255, options={"fixed" = true})
     */
    private $wxAppSecret = '';

    /**
     * @var string
     * @ORM\Column(name="wx_token", type="string", length=32, options={"fixed" = true})
     */
    private $wxToken = '';

    /**
     * @var string
     * @ORM\Column(name="wx_encoding_aes_key", type="string", length=43, options={"fixed" = true})
     */
    private $wxEncodingAESKey = '';

    /**
     * @var integer
     * @ORM\Column(name="wx_transfer_mode", type="integer")
     */
    private $wxTransferMode = self::TRANSFER_PUBLIC;

    /**
     * @var string
     * @ORM\Column(name="wx_access_token", type="text", length=512)
     */
    private $wxAccessToken = '';

    /**
     * @var integer
     * @ORM\Column(name="wx_access_token_expired", type="integer")
     */
    private $wxAccessTokenExpired = 0;

    /**
     * @var string
     * @ORM\Column(name="wx_jsapi_ticket", type="text", length=512)
     */
    private $wxJsapiTicket = '';

    /**
     * @var integer
     * @ORM\Column(name="wx_jsapi_ticket_expired", type="integer")
     */
    private $wxJsapiTicketExpired = 0;

    /**
     * @var string
     * @ORM\Column(name="wx_card_ticket", type="text", length=512)
     */
    private $wxCardTicket = '';

    /**
     * @var integer
     * @ORM\Column(name="wx_card_ticket_expired", type="integer")
     */
    private $wxCardTicketExpired = 0;

    /**
     * @var \DateTime
     * @ORM\Column(name="wx_created", type="datetime")
     */
    private $wxCreated;

    /**
     * @var Client[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Weixin\Entity\Client", mappedBy="clientWeixin", cascade={"remove"})
     */
    private $wxClients;

    /**
     * @var Menu[] | ArrayCollection
     * @ORM\OneToMany(targetEntity="Weixin\Entity\Menu", mappedBy="menuWeixin", cascade={"remove"})
     */
    private $wxMenus;

    /**
     * @var Tag[] | ArrayCollection
     * @ORM\OneToMany(targetEntity="Weixin\Entity\Tag", mappedBy="tagWeixin", cascade={"remove"})
     */
    private $wxTags;

    /**
     * @var QRCode[] | ArrayCollection
     * @ORM\OneToMany(targetEntity="Weixin\Entity\QRCode", mappedBy="qrcodeWeixin", cascade={"remove"})
     */
    private $wxQRCodes;

    /**
     * @var Event[] | ArrayCollection
     * @ORM\OneToMany(targetEntity="Weixin\Entity\Event", mappedBy="eventWeixin", cascade={"remove"})
     */
    private $wxEvents;



    public function __construct()
    {
        $this->wxClients = new ArrayCollection();
        $this->wxMenus = new ArrayCollection();
        $this->wxTags = new ArrayCollection();
        $this->wxQRCodes = new ArrayCollection();
        $this->wxEvents = new ArrayCollection();
    }

    /**
     * @return array
     */
    public static function TransferModeStringsList()
    {
        return [
            self::TRANSFER_PUBLIC => '明文模式',
            self::TRANSFER_COMPATIBLE => '兼容模式',
            self::TRANSFER_ENCODING => '加密模式',
        ];
    }

    /**
     * @return string
     */
    public function getWxTransferModeAsString()
    {
        $list = self::TransferModeStringsList();
        return isset($list[$this->wxTransferMode]) ? $list[$this->wxTransferMode] : '未知模式';
    }


    /**
     * @return int
     */
    public function getWxID()
    {
        return $this->wxID;
    }

    /**
     * @return string
     */
    public function getWxName()
    {
        return $this->wxName;
    }

    /**
     * @param string $wxName
     */
    public function setWxName($wxName)
    {
        $this->wxName = $wxName;
    }

    /**
     * @return string
     */
    public function getWxAppID()
    {
        return $this->wxAppID;
    }

    /**
     * @param string $wxAppID
     */
    public function setWxAppID($wxAppID)
    {
        $this->wxAppID = $wxAppID;
    }

    /**
     * @return string
     */
    public function getWxAppSecret()
    {
        return $this->wxAppSecret;
    }

    /**
     * @param string $wxAppSecret
     */
    public function setWxAppSecret($wxAppSecret)
    {
        $this->wxAppSecret = $wxAppSecret;
    }

    /**
     * @return string
     */
    public function getWxToken()
    {
        return $this->wxToken;
    }

    /**
     * @param string $wxToken
     */
    public function setWxToken($wxToken)
    {
        $this->wxToken = $wxToken;
    }

    /**
     * @return string
     */
    public function getWxEncodingAESKey()
    {
        return $this->wxEncodingAESKey;
    }

    /**
     * @param string $wxEncodingAESKey
     */
    public function setWxEncodingAESKey($wxEncodingAESKey)
    {
        $this->wxEncodingAESKey = $wxEncodingAESKey;
    }

    /**
     * @return int
     */
    public function getWxTransferMode()
    {
        return $this->wxTransferMode;
    }

    /**
     * @param int $wxTransferMode
     */
    public function setWxTransferMode($wxTransferMode)
    {
        $this->wxTransferMode = $wxTransferMode;
    }

    /**
     * @return string
     */
    public function getWxAccessToken()
    {
        return $this->wxAccessToken;
    }

    /**
     * @param string $wxAccessToken
     */
    public function setWxAccessToken($wxAccessToken)
    {
        $this->wxAccessToken = $wxAccessToken;
    }

    /**
     * @return int
     */
    public function getWxAccessTokenExpired()
    {
        return $this->wxAccessTokenExpired;
    }

    /**
     * @param int $wxAccessTokenExpired
     */
    public function setWxAccessTokenExpired($wxAccessTokenExpired)
    {
        $this->wxAccessTokenExpired = $wxAccessTokenExpired;
    }

    /**
     * @return string
     */
    public function getWxJsapiTicket()
    {
        return $this->wxJsapiTicket;
    }

    /**
     * @param string $wxJsapiTicket
     */
    public function setWxJsapiTicket($wxJsapiTicket)
    {
        $this->wxJsapiTicket = $wxJsapiTicket;
    }

    /**
     * @return int
     */
    public function getWxJsapiTicketExpired()
    {
        return $this->wxJsapiTicketExpired;
    }

    /**
     * @param int $wxJsapiTicketExpired
     */
    public function setWxJsapiTicketExpired($wxJsapiTicketExpired)
    {
        $this->wxJsapiTicketExpired = $wxJsapiTicketExpired;
    }

    /**
     * @return string
     */
    public function getWxCardTicket()
    {
        return $this->wxCardTicket;
    }

    /**
     * @param string $wxCardTicket
     */
    public function setWxCardTicket($wxCardTicket)
    {
        $this->wxCardTicket = $wxCardTicket;
    }

    /**
     * @return int
     */
    public function getWxCardTicketExpired()
    {
        return $this->wxCardTicketExpired;
    }

    /**
     * @param int $wxCardTicketExpired
     */
    public function setWxCardTicketExpired($wxCardTicketExpired)
    {
        $this->wxCardTicketExpired = $wxCardTicketExpired;
    }

    /**
     * @return \DateTime
     */
    public function getWxCreated()
    {
        return $this->wxCreated;
    }

    /**
     * @param \DateTime $wxCreated
     */
    public function setWxCreated($wxCreated)
    {
        $this->wxCreated = $wxCreated;
    }

    /**
     * @return ArrayCollection|Client[]
     */
    public function getWxClients()
    {
        return $this->wxClients;
    }

    /**
     * @param ArrayCollection|Client[] $wxClients
     */
    public function setWxClients($wxClients)
    {
        $this->wxClients = $wxClients;
    }

    /**
     * @return ArrayCollection|Menu[]
     */
    public function getWxMenus()
    {
        return $this->wxMenus;
    }

    /**
     * @param ArrayCollection|Menu[] $wxMenus
     */
    public function setWxMenus($wxMenus)
    {
        $this->wxMenus = $wxMenus;
    }

    /**
     * @return ArrayCollection|Tag[]
     */
    public function getWxTags()
    {
        return $this->wxTags;
    }

    /**
     * @param ArrayCollection|Tag[] $wxTags
     */
    public function setWxTags($wxTags)
    {
        $this->wxTags = $wxTags;
    }

    /**
     * @return ArrayCollection|QRCode[]
     */
    public function getWxQRCodes()
    {
        return $this->wxQRCodes;
    }

    /**
     * @param ArrayCollection|QRCode[] $wxQRCodes
     */
    public function setWxQRCodes($wxQRCodes)
    {
        $this->wxQRCodes = $wxQRCodes;
    }

    /**
     * @return ArrayCollection|Event[]
     */
    public function getWxEvents()
    {
        return $this->wxEvents;
    }

    /**
     * @param ArrayCollection|Event[] $wxEvents
     */
    public function setWxEvents($wxEvents)
    {
        $this->wxEvents = $wxEvents;
    }



}
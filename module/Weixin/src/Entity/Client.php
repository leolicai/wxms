<?php
/**
 * Client.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Client
 * @package Weixin\Entity
 *
 * @ORM\Entity(repositoryClass="\Weixin\Repository\ClientRepository")
 * @ORM\Table(name="wx_client")
 */
class Client
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="client_id", type="string", length=36, options={"fixed" = true})
     */
    private $clientID;

    /**
     * @var string
     * @ORM\Column(name="client_name", type="string", length=45)
     */
    private $clientName = '';

    /**
     * @var string
     * @ORM\Column(name="client_ip", type="string", length=255)
     */
    private $clientIp = '';

    /**
     * @var string
     * @ORM\Column(name="client_domain", type="string", length=255)
     */
    private $cientDomain = '';

    /**
     * @var string
     * @ORM\Column(name="client_api", type="string", length=255)
     */
    private $clientApi = '';

    /**
     * @var \DateTime
     * @ORM\Column(name="client_start", type="datetime")
     */
    private $clientStart;

    /**
     * @var \DateTime
     * @ORM\Column(name="client_expired", type="datetime")
     */
    private $clientExpired;

    /**
     * @var \DateTime
     * @ORM\Column(name="client_created", type="datetime")
     */
    private $clientCreated;

    /**
     * @var Weixin
     * @ORM\ManyToOne(targetEntity="Weixin\Entity\Weixin", inversedBy="wxClients")
     * @ORM\JoinColumn(name="wx_id", referencedColumnName="wx_id")
     */
    private $clientWeixin;

    /**
     * @return string
     */
    public function getClientID()
    {
        return $this->clientID;
    }

    /**
     * @param string $clientID
     */
    public function setClientID($clientID)
    {
        $this->clientID = $clientID;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @return string
     */
    public function getCientDomain()
    {
        return $this->cientDomain;
    }

    /**
     * @param string $cientDomain
     */
    public function setCientDomain($cientDomain)
    {
        $this->cientDomain = $cientDomain;
    }

    /**
     * @return string
     */
    public function getClientApi()
    {
        return $this->clientApi;
    }

    /**
     * @param string $clientApi
     */
    public function setClientApi($clientApi)
    {
        $this->clientApi = $clientApi;
    }

    /**
     * @return \DateTime
     */
    public function getClientStart()
    {
        return $this->clientStart;
    }

    /**
     * @param \DateTime $clientStart
     */
    public function setClientStart($clientStart)
    {
        $this->clientStart = $clientStart;
    }

    /**
     * @return \DateTime
     */
    public function getClientExpired()
    {
        return $this->clientExpired;
    }

    /**
     * @param \DateTime $clientExpired
     */
    public function setClientExpired($clientExpired)
    {
        $this->clientExpired = $clientExpired;
    }

    /**
     * @return \DateTime
     */
    public function getClientCreated()
    {
        return $this->clientCreated;
    }

    /**
     * @param \DateTime $clientCreated
     */
    public function setClientCreated($clientCreated)
    {
        $this->clientCreated = $clientCreated;
    }

    /**
     * @return Weixin
     */
    public function getClientWeixin()
    {
        return $this->clientWeixin;
    }

    /**
     * @param Weixin $clientWeixin
     */
    public function setClientWeixin($clientWeixin)
    {
        $this->clientWeixin = $clientWeixin;
    }




}
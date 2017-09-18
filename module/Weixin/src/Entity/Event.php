<?php
/**
 * Event.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/18
 * Version: 1.0
 */

namespace Weixin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Event
 * @package Weixin\Entity
 * @ORM\Entity(repositoryClass="\Weixin\Repository\EventRepository")
 * @ORM\Table(
 *     name="wx_event",
 *     indexes={
 *         @ORM\Index(name="event_type_idx", columns={"event_type"}),
 *         @ORM\Index(name="event_target_idx", columns={"event_target"})
 *     }
 * )
 */
class Event
{

    const EVENT_SUBSCRIBE = 'subscribe';
    const EVENT_SCAN = 'scan';
    const EVENT_CLICK = 'click';
    const EVENT_TEXT = 'text';

    const HANDLE_RESPONSE = 1;
    const HANDLE_TRANSFER = 2;


    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="event_id", type="string", length=36, options={"fixed" = true})
     */
    private $eventID;

    /**
     * @var string
     * @ORM\Column(name="event_type", type="string", length=32, options={"fixed" = true})
     */
    private $eventType = '';

    /**
     * @var string
     * @ORM\Column(name="event_target", type="string", length=64, options={"fixed" = true})
     */
    private $eventTarget = '';

    /**
     * @var string
     * @ORM\Column(name="event_result", type="text", length=65535)
     */
    private $eventResult = '';

    /**
     * @var integer
     * @ORM\Column(name="event_handle", type="integer")
     */
    private $eventHandle = self::HANDLE_RESPONSE;


    /**
     * @var Weixin
     * @ORM\ManyToOne(targetEntity="Weixin\Entity\Weixin", inversedBy="wxEvents")
     * @ORM\JoinColumn(name="wx_id", referencedColumnName="wx_id")
     */
    private $eventWeixin;

    /**
     * @return array
     */
    public static function EventTypeStringList()
    {
        return [
            self::EVENT_SUBSCRIBE => '用户关注事件',
            self::EVENT_SCAN => '用户扫描二维码事件',
            self::EVENT_CLICK => '用户点击菜单事件',
            self::EVENT_TEXT => '用户发送消息事件',
        ];
    }

    /**
     * @return string
     */
    public function getEventTypeAsString()
    {
        $list = self::EventTypeStringList();
        return isset($list[$this->eventType]) ?: '未知事件';
    }

    /**
     * @return array
     */
    public static function EventHandleStringList()
    {
        return [
            self::HANDLE_RESPONSE => '回复内容来自模板',
            self::HANDLE_TRANSFER => '回复内容来自转发接口',
        ];
    }

    /**
     * @return string
     */
    public function getEventHandleAsString()
    {
        $list = self::EventHandleStringList();
        return isset($list[$this->eventHandle]) ?: '未知内容';
    }

    /**
     * @return string
     */
    public function getEventID()
    {
        return $this->eventID;
    }

    /**
     * @param string $eventID
     */
    public function setEventID($eventID)
    {
        $this->eventID = $eventID;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return string
     */
    public function getEventTarget()
    {
        return $this->eventTarget;
    }

    /**
     * @param string $eventTarget
     */
    public function setEventTarget($eventTarget)
    {
        $this->eventTarget = $eventTarget;
    }

    /**
     * @return string
     */
    public function getEventResult()
    {
        return $this->eventResult;
    }

    /**
     * @param string $eventResult
     */
    public function setEventResult($eventResult)
    {
        $this->eventResult = $eventResult;
    }

    /**
     * @return integer
     */
    public function getEventHandle()
    {
        return $this->eventHandle;
    }

    /**
     * @param integer $eventHandle
     */
    public function setEventHandle($eventHandle)
    {
        $this->eventHandle = $eventHandle;
    }

    /**
     * @return Weixin
     */
    public function getEventWeixin()
    {
        return $this->eventWeixin;
    }

    /**
     * @param Weixin $eventWeixin
     */
    public function setEventWeixin($eventWeixin)
    {
        $this->eventWeixin = $eventWeixin;
    }

}
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
     * @ORM\Column(name="event_response", type="text", length=65535)
     */
    private $eventReponse;

    /**
     * @var string
     * @ORM\Column(name="event_transfer", type="string", length=255)
     */
    private $eventTransfer;


    /**
     * @var Weixin
     * @ORM\ManyToOne(targetEntity="Weixin\Entity\Weixin", inversedBy="wxEvents")
     * @ORM\JoinColumn(name="wx_id", referencedColumnName="wx_id")
     */
    private $eventWeixin;

}
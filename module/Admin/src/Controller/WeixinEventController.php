<?php
/**
 * WeixinEventController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/18
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\InvalidArgumentException;
use Admin\Form\WeixinEventForm;
use Ramsey\Uuid\Uuid;
use Weixin\Entity\Event;
use Weixin\Entity\Weixin;


class WeixinEventController extends AdminBaseController
{

    public function indexAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $wxManager = $this->appWeixinManager();
        $weixin = $wxManager->getWeixin($wxID);
        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $this->addResultData('weixin', $weixin);
        $this->addResultData('activeID', WeixinController::class);
    }


    /**
     * Delete a event
     */
    public function deleteAction()
    {
        $eventID = $this->params()->fromRoute('key', '');

        $wxManager = $this->appWeixinManager();
        $event = $wxManager->getEvent($eventID);

        if (! $event instanceof Event) {
            throw new InvalidArgumentException('Invalid event identity');
        }

        $wxManager->removeEvent($event);

        $this->go(
            '事件已删除',
            '事件已经删除. 已在公众号消息系统中生效.',
            $this->url()->fromRoute('admin/weixin-event', ['action' => 'index', 'key' => $event->getEventWeixin()->getWxID()])
        );
        return $this->layout()->setTerminal(true);
    }


    /**
     * Edit a event
     */
    public function editAction()
    {
        $eventID = $this->params()->fromRoute('key', '');

        $wxManager = $this->appWeixinManager();
        $event = $wxManager->getEvent($eventID);

        if (! $event instanceof Event) {
            throw new InvalidArgumentException('Invalid event identity');
        }

        $form = new WeixinEventForm(true);
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $event->setEventHandle($data[WeixinEventForm::FIELD_HANDLE]);
                $event->setEventResult($data[WeixinEventForm::FIELD_RESULT]);
                $wxManager->saveModifiedEvent($event);

                $this->go(
                    '事件已更新',
                    '事件已经更新完成, 并且已经生效.',
                    $this->url()->fromRoute('admin/weixin-event', ['action' => 'index', 'key' => $event->getEventWeixin()->getWxID()])
                );
                return $this->layout()->setTerminal(true);
            }
        }

        $this->addResultData('form', $form);
        $this->addResultData('event', $event);
        $this->addResultData('activeID', WeixinController::class);

    }


    /**
     * Add a event
     */
    public function addAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $wxManager = $this->appWeixinManager();
        $wx = $wxManager->getWeixin($wxID);
        if (! $wx instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $form = new WeixinEventForm();
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $eventType = $data[WeixinEventForm::FIELD_TYPE];
                $eventTarget = $data[WeixinEventForm::FIELD_TARGET];
                $eventHandle = $data[WeixinEventForm::FIELD_HANDLE];
                $eventResult = $data[WeixinEventForm::FIELD_RESULT];

                $event = $wxManager->getWeixinEvent($wx, $eventType, $eventTarget);
                if ($event instanceof Event) {
                    $event->setEventHandle($eventHandle);
                    $event->setEventResult($eventResult);
                } else {
                    $event = new Event();
                    $event->setEventID(Uuid::uuid1()->toString());
                    $event->setEventType($eventType);
                    $event->setEventTarget($eventTarget);
                    $event->setEventHandle($eventHandle);
                    $event->setEventResult($eventResult);
                    $event->setEventWeixin($wx);
                }

                $wxManager->saveModifiedEvent($event);

                $this->go(
                    '事件已创建',
                    '事件已经创建完成, 并且已经生效.',
                    $this->url()->fromRoute('admin/weixin-event', ['action' => 'index', 'key' => $wx->getWxID()])
                );
                return $this->layout()->setTerminal(true);
            }
        }

        $this->addResultData('form', $form);
        $this->addResultData('weixin', $wx);
        $this->addResultData('activeID', WeixinController::class);

    }

}
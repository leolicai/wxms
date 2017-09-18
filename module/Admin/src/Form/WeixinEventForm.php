<?php
/**
 * WeixinEventForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/18
 * Version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;
use Weixin\Entity\Event;


class WeixinEventForm extends BaseForm
{

    const FIELD_TYPE = 'etype';
    const FIELD_TARGET = 'etarget';
    const FIELD_HANDLE = 'ehandle';
    const FIELD_RESULT = 'eresult';

    private $editMode = false;

    public function __construct($editMode = false)
    {
        $this->editMode = $editMode;
        parent::__construct();
    }


    /**
     * Event type
     */
    private function addEventType()
    {
        $this->addSelectElement(self::FIELD_TYPE, Event::EventTypeStringList());
    }

    /**
     * Event target
     */
    private function addEventTarget()
    {
        $validators = [
            Factory::StringLength(1, 64),
        ];

        $this->addTextElement(self::FIELD_TARGET, true, $validators);
    }

    /**
     * Event handle type
     */
    private function addEventHandle()
    {
        $this->addSelectElement(self::FIELD_HANDLE, Event::EventHandleStringList());
    }

    /**
     * Event result
     */
    private function addEventResult()
    {
        $this->addTextareaElement(self::FIELD_RESULT);
    }

    /**
     * @Override
     */
    public function addElements()
    {
        if (!$this->editMode) {
            $this->addEventType();
            $this->addEventTarget();
        }

        $this->addEventHandle();
        $this->addEventResult();
    }

}
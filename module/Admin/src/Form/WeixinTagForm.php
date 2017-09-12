<?php
/**
 * WeixinTagForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/12
 * Version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class WeixinTagForm extends BaseForm
{
    const FIELD_NAME = 'name';


    private function addTagName()
    {
        $validators = [
            Factory::StringLength(1, 15),
        ];

        $this->addTextElement(self::FIELD_NAME, true, $validators);
    }


    public function addElements()
    {
        $this->addTagName();
    }


}
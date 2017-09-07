<?php
/**
 * UpdateProfileForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class UpdateProfileForm extends BaseForm
{

    const FIELD_NAME = 'name';

    /**
     * 表单: 用户名字
     */
    private function addUpdateProfileName()
    {
        $validators = [
            Factory::StringLength(2, 15),
        ];

        $this->addTextElement(self::FIELD_NAME, true, $validators);
    }


    public function addElements()
    {
        $this->addUpdateProfileName();
    }

}
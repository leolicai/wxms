<?php
/**
 * AdminerForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/30
 * Version: 1.0
 */

namespace Admin\Form;


use Admin\Entity\Adminer;
use Admin\Service\AdminerManager;
use Admin\Validator\AdminerEmailUniqueValidator;
use Form\Form\BaseForm;
use Form\Validator\Factory;


class AdminerForm extends BaseForm
{

    const FIELD_EMAIL = 'email';
    const FIELD_PASSWORD = 'password';
    const FIELD_NAME = 'name';
    const FIELD_EXPIRED = 'expired';
    const FIELD_LEVEL = 'level';

    /**
     * @var AdminerManager
     */
    private $adminerManager;

    /**
     * @var Adminer
     */
    private $adminer;

    /**
     * @var array
     */
    private $fields;


    public function __construct(AdminerManager $adminerManager, $adminer = null, $fields = ['*'])
    {

        $this->adminerManager = $adminerManager;
        $this->adminer = $adminer;
        $this->fields = $fields;

        parent::__construct();
    }


    /**
     * 表单: 用户帐号
     */
    private function addAdminerAccount()
    {
        $validators = [
            [
                'name' => AdminerEmailUniqueValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'adminerManager' => $this->adminerManager,
                    'adminer' => $this->adminer,
                ],
            ]
        ];

        $this->addEmailElement(self::FIELD_EMAIL, true, $validators);
    }


    /**
     * 表单: 用户密码
     */
    private function addAdminerPassword()
    {
        $validators = [
            Factory::StringLength(4, 15),
        ];

        $this->addPasswordElement(self::FIELD_PASSWORD, $validators);
    }


    /**
     * 表单: 用户名字
     */
    private function addAdminerName()
    {
        $validators = [
            Factory::StringLength(2, 15),
        ];

        $this->addTextElement(self::FIELD_NAME, true, $validators);
    }


    /**
     * 表单: 账号失效时间
     */
    private function addAdminerExpired()
    {
        $this->addDateElement(self::FIELD_EXPIRED);
    }


    /**
     * 表单: 用户等级
     */
    private function addAdminerLevel()
    {
        $options = [
            Adminer::LEVEL_DEFAULT      => Adminer::LevelList()[Adminer::LEVEL_DEFAULT],
            Adminer::LEVEL_INTERIOR     => Adminer::LevelList()[Adminer::LEVEL_INTERIOR],
            Adminer::LEVEL_JUNIOR       => Adminer::LevelList()[Adminer::LEVEL_JUNIOR],
            Adminer::LEVEL_SENIOR       => Adminer::LevelList()[Adminer::LEVEL_SENIOR],
            Adminer::LEVEL_SUPERIOR     => Adminer::LevelList()[Adminer::LEVEL_SUPERIOR],
        ];

        $this->addSelectElement(self::FIELD_LEVEL, $options);
    }


    public function addElements()
    {
        if (in_array('*', $this->fields) || in_array(self::FIELD_EMAIL, $this->fields)) {
            $this->addAdminerAccount();
        }

        if (in_array('*', $this->fields) || in_array(self::FIELD_PASSWORD, $this->fields)) {
            $this->addAdminerPassword();
        }

        if (in_array('*', $this->fields) || in_array(self::FIELD_NAME, $this->fields)) {
            $this->addAdminerName();
        }

        if (in_array('*', $this->fields) || in_array(self::FIELD_EXPIRED, $this->fields)) {
            $this->addAdminerExpired();
        }

        if (in_array('*', $this->fields) || in_array(self::FIELD_LEVEL, $this->fields)) {
            $this->addAdminerLevel();
        }
    }

}
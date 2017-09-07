<?php
/**
 * UpdatePasswordForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\Form;


use Admin\Service\AdminerManager;
use Admin\Service\AuthService;
use Admin\Validator\OldPasswordValidator;
use Form\Form\BaseForm;
use Form\Validator\Factory;


class UpdatePasswordForm extends BaseForm
{

    const FIELD_OLD_PASSWORD = 'old_password';
    const FIELD_NEW_PASSWORD = 'new_password';
    const FIELD_RE_NEW_PASSWORD = 're_new_password';

    /**
     * @var AdminerManager
     */
    private $adminerManager;

    /**
     * @var AuthService
     */
    private $authService;


    public function __construct(AdminerManager $adminerManager, AuthService $authService)
    {
        $this->adminerManager = $adminerManager;
        $this->authService = $authService;

        parent::__construct();
    }


    /**
     * 表单: 用户旧密码
     */
    private function addOldPassword()
    {
        $validators = [
            Factory::StringLength(4, 15),
            [
                'name'    => OldPasswordValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'adminerManager' => $this->adminerManager,
                    'authService' => $this->authService,
                ],
            ]
        ];

        $this->addPasswordElement(self::FIELD_OLD_PASSWORD, $validators);
    }


    /**
     * 表单: 用户新密码
     */
    private function addNewPassword()
    {
        $validators = [
            Factory::StringLength(4, 15),
        ];

        $this->addPasswordElement(self::FIELD_NEW_PASSWORD, $validators);
    }

    /**
     * 表单: 用户确认密码
     */
    private function addConfirmPassword()
    {
        $validators = [
            Factory::Identical(self::FIELD_NEW_PASSWORD),
        ];

        $this->addPasswordElement(self::FIELD_RE_NEW_PASSWORD, $validators);
    }


    public function addElements()
    {
        $this->addOldPassword();
        $this->addNewPassword();
        $this->addConfirmPassword();
    }

}
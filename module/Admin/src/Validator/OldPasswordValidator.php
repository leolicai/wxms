<?php
/**
 * OldPasswordValidator.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\Validator;


use Admin\Entity\Adminer;
use Admin\Service\AdminerManager;
use Admin\Service\AuthService;
use Zend\Validator\AbstractValidator;


class OldPasswordValidator extends AbstractValidator
{

    // Message IDs
    const OLD_PWD_INVALID = 'oldPwdInvalid';

    /**
     * Validator options
     *
     * @var array
     */
    private $options = [
        'adminerManager' => null,
        'authService' => null,
    ];


    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::OLD_PWD_INVALID => '您的当前密码输入不正确',
    ];


    /**
     * OldPasswordValidator constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['adminerManager'])) {
                $this->options['adminerManager'] = $options['adminerManager'];
            }
            if (isset($options['authService'])) {
                $this->options['authService'] = $options['authService'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Validate the value
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $adminerManager = $this->options['adminerManager'];
        if (!$adminerManager instanceof AdminerManager) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        $authService = $this->options['authService'];
        if (!$authService instanceof AuthService) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        $adminer = $adminerManager->getAdminerByID($authService->getIdentity());
        if (!$adminer instanceof Adminer) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        if (md5($value) != $adminer->getAdminPasswd()) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        return true;
    }

}
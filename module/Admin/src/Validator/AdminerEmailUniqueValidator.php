<?php
/**
 * AdminerEmailUniqueValidator.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/30
 * Version: 1.0
 */

namespace Admin\Validator;


use Admin\Entity\Adminer;
use Admin\Service\AdminerManager;
use Zend\Validator\AbstractValidator;


class AdminerEmailUniqueValidator extends AbstractValidator
{

    const EMAIL_EXISTED = 'emailExisted';

    protected $options = [
        'adminerManager' => null,
        'adminer' => null,
    ];

    protected $messageTemplates = [
        self::EMAIL_EXISTED => '该电子邮件地址已经被使用',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['adminerManager'])) {
                $this->options['adminerManager'] = $options['adminerManager'];
            }
            if (isset($options['adminer'])) {
                $this->options['adminer'] = $options['adminer'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the mail address is unique.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $adminerManager = $this->options['adminerManager'];
        if (!$adminerManager instanceof AdminerManager) {
            $this->error(self::EMAIL_EXISTED);
            return false;
        }

        $adminer = $this->options['adminer'];

        $existedAdminer = $adminerManager->getAdminerByEmail($value);

        if (!$adminer instanceof Adminer) {
            if (!$existedAdminer instanceof Adminer) {
                return true;
            } else {
                $this->error(self::EMAIL_EXISTED);
                return false;
            }
        } else {
            $email = $adminer->getAdminEmail();
            if ($email == $value) {
                return true;
            } else {
                if (!$existedAdminer instanceof Adminer) {
                    return true;
                } else {
                    $this->error(self::EMAIL_EXISTED);
                    return false;
                }
            }
        }
    }

}
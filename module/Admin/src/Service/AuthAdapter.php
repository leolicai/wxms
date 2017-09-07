<?php
/**
 * AuthAdapter.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/28
 * Version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\Adminer;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;


class AuthAdapter implements AdapterInterface
{
    const FAILURE_CREDENTIAL_INVALID = 100;
    const FAILURE_IDENTITY_NOT_FOUND = 101;
    const FAILURE_NOT_ACTIVATED = 102;
    const FAILURE_LOCKED = 103;
    const FAILURE_EXPIRED = 104;
    const FAILURE_DISABLED = 105;
    const SUCCESS = 1;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $passwd;

    /**
     * @var AdminerManager
     */
    private $adminerManager;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * @param string $passwd
     */
    public function setPasswd($passwd)
    {
        $this->passwd = md5(strtolower($passwd));
    }

    /**
     * @return AdminerManager
     */
    public function getAdminerManager()
    {
        return $this->adminerManager;
    }

    /**
     * @param AdminerManager $adminerManager
     */
    public function setAdminerManager($adminerManager)
    {
        $this->adminerManager = $adminerManager;
    }


    /**
     * @return Result
     */
    public function authenticate()
    {
        $adminer = $this->getAdminerManager()->getAdminerByEmail($this->getEmail());

        // Existed
        if (!$adminer instanceof Adminer) {
            return new Result(self::FAILURE_IDENTITY_NOT_FOUND, null, ['IdentityNotFound']);
        }

        // Activated
        if (Adminer::ACTIVATED_VALID != $adminer->getAdminActivated()) {
            return new Result(self::FAILURE_NOT_ACTIVATED, null, ['Inactivated']);
        }

        // Locked
        if (Adminer::LOCKED_VALID == $adminer->getAdminLocked()) {
            return new Result(self::FAILURE_LOCKED, null, ['Locked']);
        }

        // Expired
        $nowDate = new \DateTime();
        if ($nowDate > $adminer->getAdminExpired()) {
            return new Result(self::FAILURE_EXPIRED, null, ['Expired']);
        }

        // Status
        if (Adminer::STATUS_VALID != $adminer->getAdminStatus()) {
            return new Result(self::FAILURE_DISABLED, null, ['invalid']);
        }

        // Password
        if ($this->getPasswd() != $adminer->getAdminPasswd()) {
            return new Result(self::FAILURE_CREDENTIAL_INVALID, null, ['PasswordInvalid']);
        }

        return new Result(self::SUCCESS, $adminer->getAdminID(), ['Success']);
    }
}
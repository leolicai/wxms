<?php
/**
 * AppIdUniqueValidator.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Validator;


use Weixin\Entity\Weixin;
use Weixin\Service\WeixinManager;
use Zend\Validator\AbstractValidator;


class AppIdUniqueValidator extends AbstractValidator
{

    const APPID_EXISTED = 'appIdExisted';

    protected $options = [
        'weixinManager' => null,
        'weixin' => null,
    ];

    protected $messageTemplates = [
        self::APPID_EXISTED => '此微信 AppID 已经被使用了.',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['weixinManager'])) {
                $this->options['weixinManager'] = $options['weixinManager'];
            }
            if (isset($options['weixin'])) {
                $this->options['weixin'] = $options['weixin'];
            }
        }

        parent::__construct($options);
    }

    /**
     * Check the appid is unique.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {

        $weixinManager = $this->options['weixinManager'];
        if (!$weixinManager instanceof WeixinManager) {
            $this->error(self::APPID_EXISTED);
            return false;
        }

        $weixin = $this->options['weixin'];

        $existed = $weixinManager->getWeixinByAppID($value);
        if (!$existed instanceof Weixin) {
            return true;
        } else {
            if ($weixin instanceof Weixin) {
                if ($weixin->getWxAppID() == $existed->getWxAppID()) {
                    return true;
                }
            }
            $this->error(self::APPID_EXISTED);
            return false;
        }
    }


}
<?php
/**
 * WeixinForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;
use Weixin\Entity\Weixin;
use Weixin\Service\WeixinManager;
use Weixin\Validator\AppIdUniqueValidator;


class WeixinForm extends BaseForm
{

    const FIELD_APPID = 'appid';
    const FIELD_APPSECRET = 'appsecret';

    /**
     * @var WeixinManager
     */
    private $weixinManager;

    /**
     * @var Weixin
     */
    private $weixin;

    public function __construct(WeixinManager $weixinManager, $weixin = null)
    {
        $this->weixinManager = $weixinManager;
        $this->weixin = $weixin;

        parent::__construct();
    }

    /**
     * Form field appid
     */
    private function formFieldAppID()
    {

        $validators = [
            Factory::Regex("/^wx[0-9a-z]+$/"),
            [
                'name' => AppIdUniqueValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'weixinManager' => $this->weixinManager,
                    'weixin' => $this->weixin,
                ],
            ],
        ];

        $this->addTextElement(self::FIELD_APPID, true, $validators);
    }


    /**
     * Form field appsecret
     */
    private function formFieldAppSecret()
    {
        $validators = [
            Factory::Regex("/^[0-9a-z]{18,255}$/"),
        ];
        $this->addTextElement(self::FIELD_APPSECRET, true, $validators);

    }

    public function addElements()
    {
        $this->formFieldAppID();
        $this->formFieldAppSecret();
    }

}
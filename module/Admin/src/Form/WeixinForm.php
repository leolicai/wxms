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
    const FIELD_NAME = 'name';
    const FIELD_TOKEN = 'token';
    const FIELD_ENCODING_AES_KEY = 'aes_key';
    const FIELD_TRANSFER_MODE = 'transfer_mode';

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

    /**
     * Form field name
     */
    private function formFieldName()
    {
        $validators = [
            Factory::StringLength(2, 15),
        ];

        $this->addTextElement(self::FIELD_NAME, true, $validators);
    }

    /**
     * Form field token
     */
    private function formFieldToken()
    {
        $validators = [
            Factory::StringLength(3, 32),
        ];

        $this->addTextElement(self::FIELD_TOKEN, true, $validators);
    }

    /**
     * Form field encoding aes key
     */
    private function formFieldEncodingAESKey()
    {
        $validators = [
            Factory::StringLength(1, 43),
        ];

        $this->addTextElement(self::FIELD_ENCODING_AES_KEY, true, $validators);
    }

    /**
     * Form field transfer mode
     */
    private function formFieldTransferMode()
    {
        $this->addSelectElement(self::FIELD_TRANSFER_MODE, Weixin::TransferModeStringsList());
    }


    public function addElements()
    {
        if (! $this->weixin instanceof Weixin) {
            $this->formFieldAppID();
        }
        $this->formFieldAppSecret();
        $this->formFieldName();
        $this->formFieldToken();
        $this->formFieldEncodingAESKey();
        $this->formFieldTransferMode();
    }

}
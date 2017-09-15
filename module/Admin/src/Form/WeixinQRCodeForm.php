<?php
/**
 * WeixinQRCodeForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/15
 * Version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;
use Weixin\Entity\QRCode;


class WeixinQRCodeForm extends BaseForm
{

    const FIELD_NAME = 'name';
    const FIELD_TYPE = 'type';
    const FIELD_EXPIRED = 'expired';
    const FIELD_SCENE = 'scene';


    /**
     * 表单: 二维码名字
     */
    private function addWeChatQrCodeName()
    {
        $this->addTextElement(self::FIELD_NAME, true, [Factory::StringLength(2, 45)]);
    }

    /**
     * 表单: 二维码类型
     */
    private function addWeChatQrCodeType()
    {
        $this->addSelectElement(self::FIELD_TYPE, QRCode::TypeStringList());
    }

    /**
     * 二维码过期时间
     */
    private function addWeChatQrCodeExpired()
    {
        $this->addTextElement(self::FIELD_EXPIRED, true, [Factory::Regex("/^[1-9][0-9]{1,5}$/")]);
    }

    /**
     * 二维码参数
     */
    private function addWeChatQrCodeScene()
    {
        $this->addTextElement(self::FIELD_SCENE, true, [Factory::Regex("/^[a-zA-Z0-9]{1,64}$/")]);
    }


    public function addElements()
    {
        $this->addWeChatQrCodeName();
        $this->addWeChatQrCodeType();
        $this->addWeChatQrCodeExpired();
        $this->addWeChatQrCodeScene();
    }


}
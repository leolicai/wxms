<?php
/**
 * WeixinClientForm.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class WeixinClientForm extends BaseForm
{

    const FIELD_NAME = 'name';
    const FIELD_DOMAIN = 'domain';
    const FIELD_IP = 'ip';
    const FIELD_API = 'api';
    const FIELD_START = 'start';
    const FIELD_EXPIRED = 'expired';


    /**
     * 表单: 客户端名称
     */
    private function addClientName()
    {
        $this->addTextElement(self::FIELD_NAME, true, [Factory::StringLength(4, 45)]);
    }


    /**
     * 表单: 客户端域名
     */
    private function addClientDomain()
    {
        $this->addTextElement(self::FIELD_DOMAIN, true, [Factory::Hostname()]);
    }

    /**
     * 表单: 客户端 IP
     */
    private function addClientIp()
    {
        $this->addTextElement(self::FIELD_IP, true, [Factory::Ip()]);
    }

    /**
     * 表单: 生效时间
     */
    private function addClientStart()
    {
        $this->addDateElement(self::FIELD_START);
    }

    /**
     * 表单: 过期时间
     */
    private function addClientExpire()
    {
        $this->addDateElement(self::FIELD_EXPIRED);
    }


    /**
     * 表单: APIs
     */
    private function addClientApi()
    {
        $this->addMultiCheckboxElement(self::FIELD_API);
    }


    public function addElements()
    {
        $this->addClientName();
        $this->addClientDomain();
        $this->addClientIp();
        $this->addClientStart();
        $this->addClientExpire();
        $this->addClientApi();
    }



}
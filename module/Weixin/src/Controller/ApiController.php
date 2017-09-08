<?php
/**
 * ApiController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Controller;


class ApiController extends WeixinBaseController
{
    /**
     * 提取公众号 Access Token
     */
    public function accessTokenAction()
    {
        //todo
    }

    public function jsApiTicketAction()
    {
        //todo
    }

    public function apiTicketAction()
    {
        //todo
    }

    public function jsSignAction()
    {
        //todo
    }

    public function cardSignAction()
    {
        //todo
    }


    public function userinfoAction()
    {
        //todo
    }

    public function oauthAction()
    {
        //todo
    }

    public function oauthedAction()
    {
        //todo
    }

    /**
     * @return array
     */
    public static function OpenedApi()
    {
        return [
            'access-token' => '读取微信公众号 AccessToken 信息接口',
            'js-api-ticket' => '读取微信公众号JS-SDK接口票据接口',
            'api-ticket' => '读取微信公众号卡券票据接口',
            'js-sign' => '微信公众号 JS-SDK 使用权限签名接口',
            'card-sign' => '微信公众号卡券签名接口',
            'oauth' => '网页授权接口',
        ];
    }

}
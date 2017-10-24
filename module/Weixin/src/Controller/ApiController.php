<?php
/**
 * ApiController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Controller;


use Weixin\Entity\Client;
use Weixin\Entity\Weixin;
use Weixin\Exception\InvalidArgumentException;
use Weixin\Service\NetworkService;


class ApiController extends WeixinBaseController
{
    /**
     * 生成随机字符串
     *
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function allowAccessControl()
    {
        $this->getResponse()->getHeaders()->addHeaderLine("Access-Control-Allow-Origin", "*");
    }


    private function getWx()
    {
        $wxID = (int)$this->params()->fromRoute('wx', 0);
        return $this->appWeixinManager()->getWeixin($wxID);
    }

    private function getClient()
    {
        $clientID = (string)$this->params()->fromRoute('client', '');
        return $this->appWeixinManager()->getClient($clientID);
    }


    private function checkIp(Client $client)
    {
        $requestIp = $this->appServer()->ipAddress();
        if ('*' == $client->getClientIp()) {
            return true;
        }
        return $requestIp == $client->getClientIp();
    }

    private function checkDomain(Client $client)
    {
        if ('*' == $client->getClientDomain()) {
            return true;
        }
        $url = urldecode($this->params()->fromQuery('url', ''));
        $domain = parse_url($url, PHP_URL_HOST);

        return preg_match("/" . $client->getClientDomain() . "/", $domain);
    }

    private function checkDate(Client $client)
    {
        $nowDate = new \DateTime();
        if ($client->getClientStart() > $nowDate) {
            return false;
        }
        if ($client->getClientExpired() < $nowDate) {
            return false;
        }
        return true;
    }

    private function checkApi(Client $client, $api = '')
    {
        $apis = json_decode($client->getClientApi());
        if (empty($api) || !in_array($api, $apis)) {
            return false;
        }
        return true;
    }


    public function indexAction()
    {
        $this->setResultTextData('');
    }


    /**
     * 提取公众号 Access Token
     * PATH: /weixin/api/access-token/wx_id/client_id.json => Server called
     * Limited by, api, ip, date.
     */
    public function accessTokenAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultCodeMessage(-1, 'Invalid wx id');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultCodeMessage(-2, 'Invalid client id');
            return;
        }

        if (! $this->checkApi($client, 'access-token')) {
            $this->setResultCodeMessage(-101, 'Api invalid.');
            return;
        }

        if (! $this->checkIp($client)) {
            $this->setResultCodeMessage(-201, 'IP address invalid.');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultCodeMessage(-401, 'Access time invalid.');
            return;
        }

        $token = $this->appWeixinService()->getAccessToken($wx);
        $appid = $wx->getWxAppID();

        $this->addResultData('appid', $appid);
        $this->addResultData('access_token', $token);
    }


    /**
     * 提取公众号 JSAPI ticket
     * Path: /weixin/api/js-api-ticket/wx_id/client_id.json => Server called.
     * Limited by: api, ip, date.
     */
    public function jsApiTicketAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultCodeMessage(-1, 'Invalid wx id');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultCodeMessage(-2, 'Invalid client id');
            return;
        }

        if (! $this->checkApi($client, 'js-api-ticket')) {
            $this->setResultCodeMessage(-101, 'Api invalid.');
            return;
        }

        if (! $this->checkIp($client)) {
            $this->setResultCodeMessage(-201, 'IP address invalid.');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultCodeMessage(-401, 'Access time invalid.');
            return;
        }

        $ticket = $this->appWeixinService()->getJsApiTicket($wx);
        $appid = $wx->getWxAppID();

        $this->addResultData('appid', $appid);
        $this->addResultData('ticket', $ticket);

    }


    /**
     * 提取公众号 Card ticket
     * Path: /weixin/api/api-ticket/wx_id/client_id.json => Server called.
     * Limited by: api, ip, date.
     */
    public function apiTicketAction()
    {

        $this->setResultType(self::RESPONSE_JSON);

        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultCodeMessage(-1, 'Invalid wx id');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultCodeMessage(-2, 'Invalid client id');
            return;
        }

        if (! $this->checkApi($client, 'api-ticket')) {
            $this->setResultCodeMessage(-101, 'Api invalid.');
            return;
        }

        if (! $this->checkIp($client)) {
            $this->setResultCodeMessage(-201, 'IP address invalid.');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultCodeMessage(-401, 'Access time invalid.');
            return;
        }

        $ticket = $this->appWeixinService()->getApiTicket($wx);
        $appid = $wx->getWxAppID();

        $this->addResultData('appid', $appid);
        $this->addResultData('ticket', $ticket);
    }


    /**
     * JsSign
     * Path: /weixin/api/js-sign/wx_id/client_id.json?url=urlencode('http://www.example.com/demo.html') => For server api call.
     * Limited by: api, date, domain.
     */
    public function jsSignAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultCodeMessage(-1, 'Invalid wx id');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultCodeMessage(-2, 'Invalid client id');
            return;
        }

        if (! $this->checkApi($client, 'js-sign')) {
            $this->setResultCodeMessage(-101, 'Api invalid.');
            return;
        }

        if (! $this->checkDomain($client)) {
            $this->setResultCodeMessage(-301, 'Domain invalid.');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultCodeMessage(-401, 'Access time invalid.');
            return;
        }

        $this->allowAccessControl();

        $url = urldecode($this->params()->fromQuery('url', ''));
        $ticket = $this->appWeixinService()->getJsApiTicket($wx);
        $noncestr = $this->createNonceStr();
        $timestamp = time();
        $sha1Source = "jsapi_ticket=$ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url";
        $signature = sha1($sha1Source);

        $this->addResultData('appid', $wx->getWxAppID());
        $this->addResultData('timestamp', $timestamp);
        $this->addResultData('noncestr', $noncestr);
        $this->addResultData('signature', $signature);
        $this->addResultData('url', $url);
    }



    public function cardSignAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultCodeMessage(-1, 'Invalid wx id');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultCodeMessage(-2, 'Invalid client id');
            return;
        }

        if (! $this->checkApi($client, 'card-sign')) {
            $this->setResultCodeMessage(-101, 'Api invalid.');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultCodeMessage(-401, 'Access time invalid.');
            return;
        }

        $this->setResultCodeMessage(9999, 'Not allowed!');
    }


    /**
     * 用户信息接口
     *
     * Path: /weixin/api/userinfo/wx_id/client_id.json?openid=OPENID
     */
    public function userinfoAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultCodeMessage(-1, 'Invalid wx id');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultCodeMessage(-2, 'Invalid client id');
            return;
        }

        if (! $this->checkApi($client, 'userinfo')) {
            $this->setResultCodeMessage(-101, 'Api invalid.');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultCodeMessage(-401, 'Access time invalid.');
            return;
        }

        $openid = $this->params()->fromQuery('openid', '');
        if (empty($openid)) {
            $this->setResultCodeMessage(-1001, 'Invalid openid.');
            return;
        }

        try {
            $res = $this->appWeixinService()->userinfo($wx, $openid);
            $this->addResultData('nickname', @$res['nickname']);
            $this->addResultData('sex', @$res['sex']);
            $this->addResultData('city', @$res['city']);
            $this->addResultData('province', @$res['province']);
            $this->addResultData('country', @$res['country']);
            $this->addResultData('headimgurl', @$res['headimgurl']);
        } catch (InvalidArgumentException $e) {
            $this->setResultCodeMessage($e->getCode(), $e->getMessage());
            return;
        }
    }


    /**
     * 用户是否是已关注粉丝判定
     *
     * Path: /weixin/api/member/wx_id/client_id.json?openid=OPENID
     */
    public function memberAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultCodeMessage(-1, 'Invalid wx id');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultCodeMessage(-2, 'Invalid client id');
            return;
        }

        if (! $this->checkApi($client, 'member')) {
            $this->setResultCodeMessage(-101, 'Api invalid.');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultCodeMessage(-401, 'Access time invalid.');
            return;
        }

        $openid = $this->params()->fromQuery('openid', '');
        if (empty($openid)) {
            $this->setResultCodeMessage(-1001, 'Invalid openid.');
            return;
        }

        try {
            $res = $this->appWeixinService()->userinfo($wx, $openid);
            if (isset($res['subscribe']) && 1 == $res['subscribe']) {
                $this->addResultData('member', '1');
                $this->addResultData('nickname', @$res['nickname']);
                $this->addResultData('sex', @$res['sex']);
                $this->addResultData('city', @$res['city']);
                $this->addResultData('province', @$res['province']);
                $this->addResultData('country', @$res['country']);
                $this->addResultData('headimgurl', @$res['headimgurl']);
            } else {
                $this->addResultData('member', '0');
            }
        } catch (InvalidArgumentException $e) {
            $this->setResultCodeMessage($e->getCode(), $e->getMessage());
            return;
        }

    }


    /**
     * 网页授权接口
     *
     * Path: /weixin/api/oauth/wx_id/client_id.html?type=base|userinfo&url=urlencode('http://www.example.com/demo.html')
     */
    public function oauthAction()
    {
        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultTextData('-1');
            return;
        }

        $client = $this->getClient();
        if (! $client instanceof Client) {
            $this->setResultTextData('-2');
            return;
        }

        if (! $this->checkApi($client, 'oauth')) {
            $this->setResultTextData('-101');
            return;
        }

        if (! $this->checkDomain($client)) {
            $this->setResultTextData('-301');
            return;
        }

        if (! $this->checkDate($client)) {
            $this->setResultTextData('-401');
            return;
        }

        $type = (string)$this->params()->fromQuery('type', '');
        if(!in_array($type, ['base', 'userinfo'])) {
            $this->setResultTextData('-1001');
            return;
        }

        $url = urldecode($this->params()->fromQuery('url', ''));
        $clientID = base64_encode($url);
        $state = $type;

        $appId = $wx->getWxAppID();
        $scope = 'snsapi_' . $type;
        $wxCallbackUrl = $this->url()->fromRoute('weixin/api', [
            'action' => 'oauthed',
            'wx' => $wx->getWxID(),
            'client' => $clientID,
            'suffix' => '.html'
        ]);
        $redirectUri = $this->appServer()->domain() . $wxCallbackUrl;

        $goUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $goUrl .= 'appid=' . $appId;
        $goUrl .= '&redirect_uri=' . urlencode($redirectUri);
        $goUrl .= '&response_type=code';
        $goUrl .= '&scope=' . $scope;
        $goUrl .= '&state=' . $state;
        $goUrl .= '#wechat_redirect';

        $this->redirect()->toUrl($goUrl);
    }


    /**
     * Wx callback api
     */
    public function oauthedAction()
    {
        $wx = $this->getWx();
        if (! $wx instanceof Weixin) {
            $this->setResultTextData('lost wx id');
            return;
        }

        $goUrl = base64_decode($this->params()->fromRoute('client', ''));
        if (empty($goUrl)) {
            $this->setResultTextData('lost client url');
            return;
        }

        $code = $this->params()->fromQuery('code', '');
        $type = $this->params()->fromQuery('state', '');


        $appId = $wx->getWxAppID();
        $appSecret = $wx->getWxAppSecret();

        try {
            $res = NetworkService::SnsAccessToken($appId, $appSecret, $code);
        } catch (InvalidArgumentException $e) {
            $this->setResultTextData($e->getCode());
            return;
        }

        $accessToken = $res['access_token'];
        $openid = $res['openid'];

        $extra = [
            'openid=' . $openid,
        ];

        if('userinfo' == $type) {
            try {
                $res = NetworkService::SnsUserInfo($accessToken, $openid);
                $extra[] = 'nickname=' . urlencode($res['nickname']);
                $extra[] = 'sex=' . $res['sex'];
                $extra[] = 'province=' . urlencode($res['province']);
                $extra[] = 'city=' . urlencode($res['city']);
                $extra[] = 'country=' . urlencode($res['country']);
                $extra[] = 'headimgurl=' . urlencode($res['headimgurl']);
            } catch (InvalidArgumentException $e) {
                //todo
            }
        }

        $goUrlFragment = '';
        $pos = stripos($goUrl, '#');
        if(false !== $pos) {
            $goUrlFragment = substr($goUrl, $pos);
            $goUrl = substr($goUrl, 0, $pos);
        }

        $goUrlQuery = '';
        $pos = stripos($goUrl, '?');
        if(false !== $pos) {
            $goUrlQuery = substr($goUrl, ($pos + 1));
            $goUrl = substr($goUrl, 0, $pos);
        }

        if(!empty($goUrlQuery)) {
            $queries = explode('&', $goUrlQuery);
            foreach($queries as $param) {
                if(!empty($param)) {
                    $extra[] = $param;
                }
            }
        }

        $goUrl .= '?' . implode('&', $extra) . $goUrlFragment;

        $this->redirect()->toUrl($goUrl);
    }


    /**
     * @return array
     */
    public static function OpenedApi()
    {
        return [
            'access-token' => '读取微信公众号 AccessToken 信息接口',
            'js-api-ticket' => '读取微信公众号JS-SDK接口票据接口',
            //'api-ticket' => '读取微信公众号卡券票据接口',
            'js-sign' => '微信公众号 JS-SDK 使用权限签名接口',
            //'card-sign' => '微信公众号卡券签名接口',
            'userinfo' => '读取用户信息',
            'member' => '用户是否已关注',
            'oauth' => '网页授权接口',
        ];
    }

}
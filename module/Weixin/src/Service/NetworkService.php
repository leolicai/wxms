<?php
/**
 * NetworkService.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Service;

use Weixin\Exception\InvalidArgumentException;
use Weixin\Exception\RuntimeException;
use Zend\Http\Client;
use Zend\Http\Header\ContentType;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\Header\UserAgent;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;


class NetworkService
{

    const WX_API_HOST = 'https://api.weixin.qq.com';


    ///////////////////// 帐号管理 /////////////////
    /**
     * 创建带参数微信二维码
     *
     * @param string $access_token
     * @param string $type
     * @param string|integer $scene
     * @param integer $expired
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function QrCodeCreate($access_token, $type, $scene, $expired)
    {
        $path = '/cgi-bin/qrcode/create?access_token=' . (string)$access_token;

        $post = new \stdClass();

        $sceneValue = new \stdClass();

        if ('QR_SCENE' == $type) {
            $post->expire_seconds = (int)$expired;
            $post->action_name = 'QR_SCENE';
            $sceneValue->scene_id = intval($scene);
        } else if('QR_LIMIT_SCENE' == $type) {
            $post->action_name = 'QR_LIMIT_SCENE';
            $sceneValue->scene_id = intval($scene);
        } else if('QR_LIMIT_STR_SCENE' == $type) {
            $post->action_name = 'QR_LIMIT_STR_SCENE';
            $sceneValue->scene_str = (string)$scene;
        } else {
            throw new InvalidArgumentException('无效的二维码类型: ' . $type);
        }

        $sceneObj = new \stdClass();
        $sceneObj->scene = $sceneValue;

        $post->action_info = $sceneObj;

        $res = self::SendPostRequest(self::WX_API_HOST . $path, json_encode($post));
        if (!isset($res['ticket']) || !isset($res['expire_seconds']) || !isset($res['url'])) {
            throw new InvalidArgumentException(@$res['errmsg'], @$res['errcode']);
        }

        return $res;
    }



    ///////////////////// 自定义菜单 /////////////////
    /**
     * 删除自定义菜单
     *
     * @param string $access_token
     * @return true
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function MenuRemoveDefault($access_token)
    {
        $path = '/cgi-bin/menu/delete?access_token=' . (string)$access_token;

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        $errCode = @$res['errcode'];
        if (0 != $errCode) {
            throw new InvalidArgumentException(@$res['errmsg'], $errCode);
        }

        return true;
    }

    /**
     * 删除个性化菜单
     *
     * @param $access_token
     * @param string $menuid
     * @return true
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function MenuRemoveConditional($access_token, $menuid)
    {
        $path = '/cgi-bin/menu/delconditional?access_token=' . (string)$access_token;

        $post = new \stdClass();
        $post->menuid = (string)$menuid;

        $res = self::SendPostRequest(self::WX_API_HOST . $path, json_encode($post));
        $errCode = @$res['errcode'];
        if (0 != $errCode) {
            throw new InvalidArgumentException(@$res['errmsg'], $errCode);
        }

        return true;
    }

    /**
     * 创建自定义菜单
     *
     * @param string $access_token
     * @param string $menu
     * @return true
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function MenuCreateDefault($access_token, $menu)
    {
        $path = '/cgi-bin/menu/create?access_token=' . (string)$access_token;

        $res = self::SendPostRequest(self::WX_API_HOST . $path, $menu);
        $errCode = @$res['errcode'];
        if (0 != $errCode) {
            throw new InvalidArgumentException(@$res['errmsg'], $errCode);
        }

        return true;
    }

    /**
     * 创建个性化菜单
     *
     * @param string $access_token
     * @param string $menu
     * @return string
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function MenuCreateConditional($access_token, $menu)
    {
        $path = '/cgi-bin/menu/addconditional?access_token=' . (string)$access_token;

        $res = self::SendPostRequest(self::WX_API_HOST . $path, $menu);
        if (!isset($res['menuid'])) {
            throw new InvalidArgumentException(@$res['errmsg'], @$res['errcode']);
        }

        return $res['menuid'];
    }

    /**
     * 导出公众号菜单
     *
     * @param string $access_token
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function MenuExport($access_token)
    {
        $path = '/cgi-bin/menu/get?access_token=' . (string)$access_token;

        $res = self::SendGetRequest(self::WX_API_HOST . $path);

        if (isset($res['errcode'])) {
            throw new InvalidArgumentException(@$res['errmsg'], $res['errcode']);
        }

        return $res;
    }


    ///////////////////// 用户管理 /////////////////
    /**
     * 读取用户标签
     *
     * @param string $access_token
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function UserTags($access_token)
    {
        $path = '/cgi-bin/tags/get?access_token=' . (string)$access_token;

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (!isset($res['tags'])) {
            throw new InvalidArgumentException(@$res['errmsg'], @$res['errcode']);
        }
        return (array)$res['tags'];
    }


    /**
     * 读取粉丝资料
     *
     * @param string $access_token
     * @param string $openid
     * @return array
     */
    public static function UserInfo($access_token, $openid)
    {
        $path = '/cgi-bin/user/info?access_token=' . (string)$access_token . '&openid=' . (string)$openid . '&lang=zh_CN';

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (isset($res['errcode'])) {
            throw new InvalidArgumentException(@$res['errmsg'], $res['errcode']);
        }

        return $res;
    }


    ///////////////////// 基础接口 /////////////////
    /**
     * 微信服务器 IP 地址
     *
     * @param string $access_token
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function GetCallbackHosts($access_token)
    {
        $path = '/cgi-bin/getcallbackip?access_token=' . (string)$access_token ;

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (!isset($res['ip_list'])) {
            throw new InvalidArgumentException(@$res['errmsg'], @$res['errcode']);
        }
        return (array)$res['ip_list'];
    }


    /**
     * 提取公众号 AccessToken
     *
     * @param string $appId
     * @param string $appSecret
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function GetAccessToken($appId, $appSecret)
    {
        $path = '/cgi-bin/token?grant_type=client_credential&appid=' . (string)$appId . '&secret=' . (string)$appSecret;

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (empty($res['access_token']) || empty($res['expires_in'])) {
            throw new InvalidArgumentException(@$res['errmsg'], @$res['errcode']);
        }

        return $res;
    }


    /**
     * 提取公众号 Jsapi_ticket
     *
     * @param string $accessToken
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function GetJsapiTicket($accessToken)
    {
        $path = '/cgi-bin/ticket/getticket?access_token='. (string)$accessToken .'&type=jsapi';

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (empty($res['ticket']) || empty($res['expires_in'])) {
            throw new InvalidArgumentException(@$res['errmsg'], @$res['errcode']);
        }

        return $res;
    }


    /**
     * 提取公众号 Api_ticket
     *
     * @param string $accessToken
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function GetCardTicket($accessToken)
    {
        $path = '/cgi-bin/ticket/getticket?access_token='. (string)$accessToken .'&type=wx_card';

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (empty($res['ticket']) || empty($res['expires_in'])) {
            throw new InvalidArgumentException(@$res['errmsg'], @$res['errcode']);
        }

        return $res;
    }


    ////////////////////// 网页授权接口 ////////////
    /**
     * 获取网页授权 AccessToken
     *
     * @param string $appId
     * @param string $secret
     * @param string $code
     * @return array
     */
    public static function SnsAccessToken($appId, $secret, $code)
    {
        $path = '/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';

        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (isset($res['errcode'])) {
            throw new InvalidArgumentException(@$res['errmsg'], $res['errcode']);
        }
        return $res;
    }


    /**
     * 网页授权提取用户信息
     *
     * @param string $access_token
     * @param string $openid
     * @return array
     */
    public static function SnsUserInfo($access_token, $openid)
    {
        $path = '/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $res = self::SendGetRequest(self::WX_API_HOST . $path);
        if (isset($res['errcode'])) {
            throw new InvalidArgumentException(@$res['errmsg'], $res['errcode']);
        }
        return $res;
    }


    ////////////////////// Network Request ////////////

    /**
     * @param string $url
     * @param mixed $data
     * @return array
     * @throws RuntimeException
     */
    private static function SendPostRequest($url, $data)
    {
        $res = self::SendRequest($url, $data, 'POST');
        if('{' != substr($res, 0, 1) || '}' != substr($res, -1)) {
            throw new RuntimeException('无效的 JSON 数据' . PHP_EOL . $res);
        }

        return json_decode($res, true);
    }


    /**
     * @param string $url
     * @return array
     * @throws RuntimeException
     */
    private static function SendGetRequest($url)
    {
        $res = self::SendRequest($url);
        if('{' != substr($res, 0, 1) || '}' != substr($res, -1)) {
            throw new RuntimeException('无效的 JSON 数据' . PHP_EOL . $res);
        }

        return json_decode($res, true);
    }


    /**
     *
     * $method: GET|POST
     * $headers: [
     *             'Accept-Encoding' => 'gzip, deflate',
     *             'Referer' => 'http://www.example.com/',
     *             'X-Requested-With' => 'XMLHttpRequest',
     *             ...]
     * $cookies: ['PHPSESSID' => 'a3065bd45b18847718e202f5bd6306ed', ...]
     *
     * @param string $url
     * @param mixed $data
     * @param string $method
     * @param array $headers
     * @param array $cookies
     * @return string
     * @throws RuntimeException
     */
    private static function SendRequest($url, $data = null, $method = 'GET', $headers = [], $cookies = [])
    {
        $headerContentType = new ContentType();
        $headerContentType->setMediaType('GET' == strtoupper($method) ? 'text/html' : 'application/x-www-form-urlencoded');
        $headerContentType->setCharset('UTF-8');

        $headerUserAgent = new UserAgent('Leo/1.0');

        $requestHeaders = new Headers();
        $requestHeaders->addHeader($headerContentType);
        $requestHeaders->addHeader($headerUserAgent);
        if(!empty($headers)) {
            foreach ($headers as $key => $value) {
                if($key != $headerContentType->getFieldName() && $key != $headerUserAgent->getFieldName()) {
                    $requestHeaders->addHeaderLine($key, $value);
                }
            }
        }

        if(!empty($cookies)) {
            $setCookies = [];
            foreach ($cookies as $key => $value) {
                $setCookies[] = new SetCookie($key, $value);
            }
            if(empty($setCookies)) {
                $headerCookie = new Cookie($setCookies);
                $requestHeaders->addHeader($headerCookie);
            }
        }

        $request = new Request();
        $request->setHeaders($requestHeaders);
        $request->setUri($url);
        $request->setVersion(Request::VERSION_11);

        if ($method == Request::METHOD_GET) {
            $request->setMethod(Request::METHOD_GET);
            if (is_array($data) && !empty($data)) {
                $request->setQuery(new Parameters($data));
            }
        } else {
            $request->setMethod(Request::METHOD_POST);
            if (!empty($data)) {
                if(is_array($data)) {
                    $request->setPost(new Parameters($data));
                } else {
                    $request->setContent($data);
                }
            }
        }

        $client = new Client();
        $client->setRequest($request);
        $client->setOptions([
            'maxredirects' => 0,
            'timeout' => 30,
        ]);

        $response = $client->send();

        if(!$response->isSuccess()) {
            throw new RuntimeException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response->getBody();
    }
}
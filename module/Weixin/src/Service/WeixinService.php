<?php
/**
 * WeixinService.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Service;


use Weixin\Entity\Weixin;
use Weixin\Exception\InvalidArgumentException;

class WeixinService
{

    /**
     * @var WeixinManager
     */
    private $weixinManager;

    public function __construct(WeixinManager $weixinManager)
    {
        $this->weixinManager = $weixinManager;
    }

    /**
     * Get the access token
     *
     * @param $wxID
     * @return string
     */
    public function getAccessToken($wxID)
    {
        $weixin = $this->weixinManager->getWeixin($wxID);
        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin ID');
        }

        $accessToken = $weixin->getWxAccessToken();
        if (empty($accessToken) || $weixin->getWxAccessTokenExpired() < time()) { // Auto refresh token

            $res = NetworkService::GetAccessToken($weixin->getWxAppID(), $weixin->getWxAppSecret());

            $expiredIn = $res['expires_in'] + time() - 60;
            $weixin->setWxAccessToken($res['access_token']);
            $weixin->setWxAccessTokenExpired($expiredIn);
            $this->weixinManager->saveModifiedWeixin($weixin);

            return $res['access_token'];
        }
        return $accessToken;
    }


    /**
     * Export weixin platform tags
     *
     * @param $wxID
     * @return array
     */
    public function getTags($wxID)
    {
        $token = $this->getAccessToken($wxID);
        return NetworkService::TagExport($token);
    }

}
<?php
/**
 * WeixinService.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Service;


use Weixin\Entity\Tag;
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
     * @param mixed $wxID
     * @return string
     */
    public function getAccessToken($wxID)
    {
        if (is_scalar($wxID)) {
            $weixinEntity = $this->weixinManager->getWeixin($wxID);
            if (! $weixinEntity instanceof Weixin) {
                throw new InvalidArgumentException('Invalid weixin ID');
            }
        } else {
            if (! $wxID instanceof Weixin) {
                throw new InvalidArgumentException('Invalid weixin object');
            }
            $weixinEntity = $wxID;
        }

        $accessToken = $weixinEntity->getWxAccessToken();
        if (empty($accessToken) || $weixinEntity->getWxAccessTokenExpired() < time()) { // Auto refresh token

            $res = NetworkService::GetAccessToken($weixinEntity->getWxAppID(), $weixinEntity->getWxAppSecret());

            $expiredIn = $res['expires_in'] + time() - 60;
            $weixinEntity->setWxAccessToken($res['access_token']);
            $weixinEntity->setWxAccessTokenExpired($expiredIn);
            $this->weixinManager->saveModifiedWeixin($weixinEntity);

            return $res['access_token'];
        }
        return $accessToken;
    }


    /**
     * Export weixin platform tags
     *
     * @param mixed $wxID
     * @return array
     */
    public function tagsExport($wxID)
    {
        $token = $this->getAccessToken($wxID);
        return NetworkService::TagExport($token);
    }

    /**
     * Create a new tag
     *
     * @param mixed $wxID
     * @param string $tag
     * @return array
     */
    public function tagCreate($wxID, $tag)
    {
        $token = $this->getAccessToken($wxID);
        return NetworkService::TagCreate($token, $tag);
    }

    /**
     * Update a tag
     *
     * @param mixed $wx
     * @param Tag $tag
     * @return bool
     */
    public function tagUpdate($wx, Tag $tag)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::TagUpdate($token, $tag->getTagID(), $tag->getTagName());
    }

    /**
     * Delete a tag
     *
     * @param mixed $wx
     * @param Tag $tag
     * @return bool
     */
    public function tagDelete($wx, Tag $tag)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::TagDelete($token, $tag->getTagID());
    }



}
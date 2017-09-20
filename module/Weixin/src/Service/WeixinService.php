<?php
/**
 * WeixinService.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Service;


use Weixin\Entity\Menu;
use Weixin\Entity\QRCode;
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
                throw new InvalidArgumentException('Invalid wx ID');
            }
        } else {
            if (! $wxID instanceof Weixin) {
                throw new InvalidArgumentException('Invalid wx object');
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
     * @param int|Weixin $wx
     * @return string
     */
    public function getJsApiTicket($wx)
    {
        if (is_scalar($wx)) {
            $weixinEntity = $this->weixinManager->getWeixin($wx);
            if (! $weixinEntity instanceof Weixin) {
                throw new InvalidArgumentException('Invalid wx ID');
            }
        } else {
            if (! $wx instanceof Weixin) {
                throw new InvalidArgumentException('Invalid wx object');
            }
            $weixinEntity = $wx;
        }

        $ticket = $weixinEntity->getWxJsapiTicket();
        if (empty($ticket) || $weixinEntity->getWxJsapiTicketExpired() < time()) { // Auto refresh token

            $res = NetworkService::GetJsapiTicket($this->getAccessToken($weixinEntity));

            $expiredIn = $res['expires_in'] + time() - 60;
            $weixinEntity->setWxJsapiTicket($res['ticket']);
            $weixinEntity->setWxJsapiTicketExpired($expiredIn);
            $this->weixinManager->saveModifiedWeixin($weixinEntity);

            return $res['ticket'];
        }

        return $ticket;
    }

    /**
     * @param int|Weixin $wx
     * @return string
     */
    public function getApiTicket($wx)
    {
        if (is_scalar($wx)) {
            $weixinEntity = $this->weixinManager->getWeixin($wx);
            if (! $weixinEntity instanceof Weixin) {
                throw new InvalidArgumentException('Invalid wx ID');
            }
        } else {
            if (! $wx instanceof Weixin) {
                throw new InvalidArgumentException('Invalid wx object');
            }
            $weixinEntity = $wx;
        }

        $ticket = $weixinEntity->getWxCardTicket();
        if (empty($ticket) || $weixinEntity->getWxCardTicketExpired() < time()) { // Auto refresh token

            $res = NetworkService::GetCardTicket($this->getAccessToken($weixinEntity));

            $expiredIn = $res['expires_in'] + time() - 60;
            $weixinEntity->setWxCardTicket($res['ticket']);
            $weixinEntity->setWxCardTicketExpired($expiredIn);
            $this->weixinManager->saveModifiedWeixin($weixinEntity);

            return $res['ticket'];
        }

        return $ticket;
    }


    ////////////////////// User API ///////////////////////

    /**
     * Get user information
     *
     * @param $wx
     * @param $openid
     * @return array
     */
    public function userinfo($wx, $openid)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::UserInfo($token, $openid);
    }


    ////////////////////// QRCode API ///////////////////////

    /**
     * Create a QRCode
     *
     * @param $wx
     * @param QRCode $code
     * @return array
     */
    public function qrCodeCreate($wx, QRCode $code)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::QrCodeCreate($token, $code->getQrcodeTypeForMP(), $code->getQrcodeSceneForMP(), $code->getQrcodeExpired());
    }



    ////////////////////// Menu API ///////////////////////

    /**
     * Create a conditional menu for wx platform
     *
     * @param $wx
     * @param Menu $menu
     * @return string
     */
    public function menuCreateConditional($wx, Menu $menu)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::MenuCreateConditional($token, $menu->getMenuData());
    }


    /**
     * Create a default menu for wx platform
     *
     * @param $wx
     * @param Menu $menu
     * @return true
     */
    public function menuCreateDefault($wx, Menu $menu)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::MenuCreateDefault($token, $menu->getMenuData());
    }


    /**
     * Remove a conditional menu from wx platform
     *
     * @param mix $wx
     * @param string $menuID
     * @return true
     */
    public function menuRemoveConditional($wx, $menuID)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::MenuRemoveConditional($token, $menuID);
    }


    /**
     * Remove all menus from wx platform
     *
     * @param $wx
     * @return true
     */
    public function menuTrashed($wx)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::MenuRemoveDefault($token);
    }


    /**
     * Export all menus from weixin platform
     *
     * @param $wx
     * @return array
     */
    public function menusExport($wx)
    {
        $token = $this->getAccessToken($wx);
        return NetworkService::MenuExport($token);
    }


    ////////////////////// Tags API ////////////////
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
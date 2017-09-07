<?php
/**
 * WeixinController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Admin\Controller;


use Weixin\Entity\Weixin;
use Weixin\Exception\RuntimeException as WeixinRuntimeException;
use Weixin\Exception\InvalidArgumentException as WeixinInvalidArgumentException;
use Admin\Form\WeixinForm;
use Weixin\Service\NetworkService;


class WeixinController extends AdminBaseController
{

    public function indexAction()
    {
        //todo
    }

    public function addAction()
    {
        $weixinManager = $this->appWeixinManager();

        $form = new WeixinForm($weixinManager);

        $error = null;
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $appId = $data[WeixinForm::FIELD_APPID];
                $appSecret = $data[WeixinForm::FIELD_APPSECRET];

                try {
                    $res = NetworkService::GetAccessToken($appId, $appSecret);

                    $accessToken = $res['access_token'];
                    $expiredIn = $res['expires_in'] + time() - 300;

                    $weixin = new Weixin();
                    $weixin->setWxAppID($appId);
                    $weixin->setWxAppSecret($appSecret);
                    $weixin->setWxAccessToken($accessToken);
                    $weixin->setWxAccessTokenExpired($expiredIn);
                    $weixin->setWxCreated(new \DateTime());

                    $weixinManager->saveModifiedWeixin($weixin);

                    $this->go(
                        '公众号已创建',
                        '公众号已经创建成功. 并且已经同步完成 AccessToken.',
                        $this->url()->fromRoute('admin/weixin')
                    );

                    return $this->layout()->setTerminal(true);

                } catch (WeixinInvalidArgumentException $e) {
                    $this->appLogger()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                } catch (WeixinRuntimeException $e) {
                    $this->appLogger()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                }

            }
        }

        $this->addResultData('form', $form);
        $this->addResultData('error', $error);
        $this->addResultData('activeID', __METHOD__);
    }

}
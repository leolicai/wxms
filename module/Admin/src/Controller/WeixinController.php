<?php
/**
 * WeixinController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\InvalidArgumentException;
use Admin\Exception\RuntimeException;
use Application\View\Helper\Pagination;
use Weixin\Entity\Weixin;
use Weixin\Exception\RuntimeException as WeixinRuntimeException;
use Weixin\Exception\InvalidArgumentException as WeixinInvalidArgumentException;
use Admin\Form\WeixinForm;
use Weixin\Service\NetworkService;


class WeixinController extends AdminBaseController
{

    /**
     * List all weixin appid
     */
    public function indexAction()
    {
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $weixinManager = $this->appWeixinManager();
        $count = $weixinManager->getWeixinCount();

        $pagination = $this->appViewHelperManager(Pagination::class);
        if (!$pagination instanceof Pagination) {
            throw new RuntimeException('Invalid view helper pagination');
        }

        $pageUrlTpl = $this->url()->fromRoute('admin/weixin', ['action' => 'index', 'key' => '%d']);
        $pagination->setPage($page)->setSize($size)->setCount($count)->setUrlTpl($pageUrlTpl);

        $entities = $weixinManager->getWeixinLimitByPage($page, $size);

        $this->addResultData('entities', $entities);
        $this->addResultData('activeID', __METHOD__);
    }


    /**
     * Delete a weixin appid
     */
    public function deleteAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wxID = (int)$this->params()->fromRoute('key', 0);

        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);

        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $weixinManager->removeWeixin($weixin);
    }


    /**
     * Refresh weixin access token.
     */
    public function refreshAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wxID = (int)$this->params()->fromRoute('key', 0);

        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);

        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        try {

            $res = NetworkService::GetAccessToken($weixin->getWxAppID(), $weixin->getWxAppSecret());

            $accessToken = $res['access_token'];
            $expiredIn = $res['expires_in'] + time() - 60;

            $weixin->setWxAccessToken($accessToken);
            $weixin->setWxAccessTokenExpired($expiredIn);

            $weixinManager->saveModifiedWeixin($weixin);

        } catch (WeixinInvalidArgumentException $e) {
            $this->appLogger()->exception($e);
            $this->setResultCodeMessage(-1, $e->getMessage());
        } catch (WeixinRuntimeException $e) {
            $this->appLogger()->exception($e);
            $this->setResultCodeMessage(-2, $e->getMessage());
        }

    }


    /**
     * Add a weixin appid
     */
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
                $appName = $data[WeixinForm::FIELD_NAME];

                try {
                    $res = NetworkService::GetAccessToken($appId, $appSecret);

                    $accessToken = $res['access_token'];
                    $expiredIn = $res['expires_in'] + time() - 300;

                    $weixin = new Weixin();
                    $weixin->setWxAppID($appId);
                    $weixin->setWxName($appName);
                    $weixin->setWxAppSecret($appSecret);
                    $weixin->setWxAccessToken($accessToken);
                    $weixin->setWxAccessTokenExpired($expiredIn);
                    $weixin->setWxCreated(new \DateTime());

                    $weixinManager->saveModifiedWeixin($weixin);

                    $this->go(
                        '公众号已创建',
                        '公众号 ' . $appName . ' 已经创建成功. 并且已经同步完成 AccessToken.',
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


    /**
     * Edit a weixin appid
     */
    public function editAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);

        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $form = new WeixinForm($weixinManager, $weixin);

        $error = null;
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $appSecret = $data[WeixinForm::FIELD_APPSECRET];
                $appName = $data[WeixinForm::FIELD_NAME];

                if ($appSecret == $weixin->getWxAppSecret()) {
                    $weixin->setWxName($appName);

                    $weixinManager->saveModifiedWeixin($weixin);

                    $this->go(
                        '公众号已修改',
                        '公众号 ' . $appName . ' 已经修改.',
                        $this->url()->fromRoute('admin/weixin')
                    );

                    return $this->layout()->setTerminal(true);
                }

                try {
                    $res = NetworkService::GetAccessToken($weixin->getWxAppID(), $appSecret);

                    $accessToken = $res['access_token'];
                    $expiredIn = $res['expires_in'] + time() - 300;

                    $weixin->setWxName($appName);
                    $weixin->setWxAppSecret($appSecret);
                    $weixin->setWxAccessToken($accessToken);
                    $weixin->setWxAccessTokenExpired($expiredIn);

                    $weixinManager->saveModifiedWeixin($weixin);

                    $this->go(
                        '公众号已修改',
                        '公众号 ' . $appName . ' 已经修改. 并且已经同步完成 AccessToken.',
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
        $this->addResultData('weixin', $weixin);
        $this->addResultData('error', $error);
        $this->addResultData('activeID', __CLASS__);
    }

}
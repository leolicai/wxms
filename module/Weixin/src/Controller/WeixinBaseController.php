<?php
/**
 * WeixinBaseController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Controller;


use Application\Controller\AppBaseController;
use Weixin\Service\WeixinManager;
use Weixin\Service\WeixinService;


class WeixinBaseController extends AppBaseController
{


    /**
     * @return WeixinService
     */
    public function appWeixinService()
    {
        return $this->appServiceManager(WeixinService::class);
    }

    /**
     * @return WeixinManager
     */
    public function appWeixinManager()
    {
        return $this->appServiceManager(WeixinManager::class);
    }

}
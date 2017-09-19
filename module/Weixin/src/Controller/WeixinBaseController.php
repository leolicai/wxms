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


class WeixinBaseController extends AppBaseController
{


    /**
     * @return WeixinManager
     */
    public function appWeixinManager()
    {
        return $this->appServiceManager(WeixinManager::class);
    }

}
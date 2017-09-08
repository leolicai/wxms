<?php
/**
 * WeixinClientController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\InvalidArgumentException;
use Weixin\Entity\Weixin;


class WeixinClientController extends AdminBaseController
{

    public function indexAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);

        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $this->addResultData('weixin', $weixin);
        $this->addResultData('activeID', WeixinController::class);
    }

}
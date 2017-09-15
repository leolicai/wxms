<?php
/**
 * WeixinQrcodeController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/15
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\InvalidArgumentException;
use Weixin\Entity\Weixin;


class WeixinQrcodeController extends AdminBaseController
{

    public function indexAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $wxManager = $this->appWeixinManager();
        $wx = $wxManager->getWeixin($wxID);

        if (! $wx instanceof Weixin) {
            throw new InvalidArgumentException('Invalid wx identity');
        }

        $this->addResultData('weixin', $wx);
        $this->addResultData('activeID', WeixinController::class);
    }

}
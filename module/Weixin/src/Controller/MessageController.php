<?php
/**
 * MessageController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Controller;


class MessageController extends WeixinBaseController
{

    public function receivedAction()
    {
        $this->setResultTextData('message received');
    }

}
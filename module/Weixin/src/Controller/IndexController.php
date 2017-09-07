<?php
/**
 * IndexController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Controller;


class IndexController extends WeixinBaseController
{

    public function indexAction()
    {
        $this->setResultTextData('.');
    }


    /**
     * Verify wx mp request
     */
    public function wxMpVerifyAction()
    {
        $key = (string)$this->params()->fromRoute('key', '');
        $this->setResultTextData($key);
    }

}
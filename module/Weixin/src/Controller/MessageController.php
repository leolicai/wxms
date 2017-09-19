<?php
/**
 * MessageController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Controller;


use Weixin\Entity\Weixin;

class MessageController extends WeixinBaseController
{

    public function receivedAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $wxManager = $this->appWeixinManager();
        $wx = $wxManager->getWeixin($wxID);
        if (! $wx instanceof Weixin) {
            $this->appLogger()->err(__METHOD__ . PHP_EOL . 'Invalid wx ID');
            $this->setResultTextData('');
            return;
        }

        $urlParams = $this->params()->fromQuery();
        $this->appLogger()->mixed($urlParams, 3);

        // WeChat check
        if (isset($urlParams['echostr'])) {
            $result = $this->validateWx($wx, $urlParams);
            $this->setResultTextData($result);
            return;
        }

        $postData = $this->getRequest()->getContent();
        $this->appLogger()->info($postData);

        $this->setResultTextData('');
    }


    /**
     * @param Weixin $wx
     * @param array $params
     * @return string
     */
    private function validateWx(Weixin $wx, $params = [])
    {
        $echostr = @$params['echostr'];
        if ($this->checkSignature($wx, $params)) {
            return $echostr;
        }
        return '';
    }

    /**
     * @param Weixin $wx
     * @param array $params
     * @return bool
     */
    private function checkSignature(Weixin $wx, $params = [])
    {
        $signature = @$params['signature'];
        $timestamp = @$params['timestamp'];
        $nonce = @$params['nonce'];

        $token = $wx->getWxToken();

        $tmpArr = [$token, $timestamp, $nonce];
        sort($tmpArr);
        $tmpStr = implode('', $tmpArr);
        $tmpStr = sha1($tmpStr);

        return $signature == $tmpStr;
    }

}
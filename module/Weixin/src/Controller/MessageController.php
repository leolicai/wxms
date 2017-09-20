<?php
/**
 * MessageController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Controller;


use Weixin\Entity\Event;
use Weixin\Entity\Weixin;

class MessageController extends WeixinBaseController
{


    /**
     * @param string $xml_string
     * @return bool|array
     */
    public static function ParseXml($xml_string)
    {
        libxml_disable_entity_loader(true);
        $xml = simplexml_load_string($xml_string,'SimpleXMLElement', LIBXML_NOCDATA);
        if (false === $xml) {
            return false;
        }
        return @json_decode(@json_encode($xml, JSON_UNESCAPED_UNICODE), true);
    }


    /**
     * @param string $toUser
     * @param string $fromUser
     * @param string $content
     * @return string
     */
    public static function GenerateXml($toUser, $fromUser, $content)
    {
        $xmlTpl = <<<XML
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
%s
</xml>
XML;
        return sprintf($xmlTpl, $toUser, $fromUser, time(), $content);
    }


    /**
     * Received weChat platform messages
     */
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
        $this->appLogger()->mixed($urlParams, 6); // INFO logo

        // WeChat check
        if (isset($urlParams['echostr'])) {
            $result = $this->validateWx($wx, $urlParams);
            $this->setResultTextData($result);
            return;
        }

        $postData = $this->getRequest()->getContent();
        $this->appLogger()->info(__METHOD__ . PHP_EOL . $postData);

        if ('<xml' != substr($postData, 0, 4) || 'xml>' != substr($postData, -4)) {
            $this->appLogger()->err(__METHOD__ . PHP_EOL . 'There is not a xml data');
            $this->setResultTextData('');
            return;
        }

        $data = self::ParseXml($postData);
        $this->appLogger()->mixed($data, 6);

        $msgType = @$data['MsgType'];

        if ('event' == $msgType) {
            $result = $this->responseEvent($wx, $data);
            $this->appLogger()->info(__METHOD__ . PHP_EOL . $result);
            $this->setResultTextData($result);
            return;
        }

        $this->setResultTextData('');
        return;
    }


    /**
     * @param Weixin $wx
     * @param array $data
     * @return string
     */
    private function responseEvent(Weixin $wx, $data)
    {
        $eventName = (string)@$data['Event'];
        $eventName = strtolower($eventName);
        $eventKey = '';

        $wxManager = $this->appWeixinManager();

        if ('subscribe' == $eventName) {
            if (!isset($data['EventKey']) || empty($data['EventKey'])) {
                $eventKey = 'Default';
            } else {
                $eventKey = (string)$data['EventKey'];
                $eventKey = str_replace('qrscene_', '', (string)$eventKey);
            }
        }

        if ('scan' == $eventName || 'view' == $eventName) {
            if (!isset($data['EventKey']) || empty($data['EventKey'])) {
                $eventKey = '';
            } else {
                $eventKey = (string)$data['EventKey'];
            }
        }

        $event = $wxManager->getWeixinEvent($wx, $eventName, $eventKey);
        if (! $event instanceof Event) {
            return '';
        }
        return self::GenerateXml(@$data['FromUserName'], @$data['ToUserName'], $event->getEventResult());
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
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        return $signature == $tmpStr;
    }

}
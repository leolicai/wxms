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
use Admin\Form\WeixinClientForm;
use Ramsey\Uuid\Uuid;
use Weixin\Controller\ApiController;
use Weixin\Entity\Client;
use Weixin\Entity\Weixin;


class WeixinClientController extends AdminBaseController
{

    /**
     * List client list
     */
    public function indexAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);

        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $this->addResultData('apiList', ApiController::OpenedApi());
        $this->addResultData('weixin', $weixin);
        $this->addResultData('activeID', WeixinController::class);
    }


    /**
     * Delete a client
     */
    public function deleteAction()
    {
        $clientID = $this->params()->fromRoute('key', '');
        $weixinManager = $this->appWeixinManager();

        $client = $weixinManager->getClient($clientID);

        if (! $client instanceof Client) {
            throw new InvalidArgumentException('Invalid weixin client identity');
        }

        $wxID = $client->getClientWeixin()->getWxID();

        $weixinManager->removeClient($client);

        $this->go(
            '客户端已经删除',
            '公众号接口访问客户端已经删除!',
            $this->url()->fromRoute('admin/weixin-client', ['action' => 'index', 'key' => $wxID])
        );

        return $this->layout()->setTerminal(true);

    }


    /**
     * Edit client configuration
     */
    public function editAction()
    {
        $clientID = $this->params()->fromRoute('key', '');
        $wxManager = $this->appWeixinManager();

        $client = $wxManager->getClient($clientID);
        if (! $client instanceof Client) {
            throw new InvalidArgumentException('Invalid wx client identity');
        }

        $apiList = ApiController::OpenedApi();

        $form = new WeixinClientForm();

        if($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            if ('*' == $postData[WeixinClientForm::FIELD_DOMAIN]) {
                $postData[WeixinClientForm::FIELD_DOMAIN] = Client::DOMAIN_ALL;
            }
            if ('*' == $postData[WeixinClientForm::FIELD_IP]) {
                $postData[WeixinClientForm::FIELD_IP] = Client::IP_ALL;
            }

            $form->setData($postData);
            if ($form->isValid()) {
                $data = $form->getData();

                $selectedApis = $data[WeixinClientForm::FIELD_API];
                if (empty($selectedApis)) {
                    $selectedApis = '';
                } else {
                    $selectedApis = json_encode($selectedApis);
                }

                $client->setClientName($data[WeixinClientForm::FIELD_NAME]);
                $client->setClientDomain($data[WeixinClientForm::FIELD_DOMAIN]);
                $client->setClientIp($data[WeixinClientForm::FIELD_IP]);
                $client->setClientStart(new \DateTime($data[WeixinClientForm::FIELD_START]));
                $client->setClientExpired(new \DateTime($data[WeixinClientForm::FIELD_EXPIRED]));
                $client->setClientApi($selectedApis);

                $wxManager->saveModifiedClient($client);

                $this->go(
                    '客户端已更新',
                    '客户端: ' . $client->getClientName() . ' 配置已经更新成功!',
                    $this->url()->fromRoute('admin/weixin-client', ['action' => 'index', 'key' => $client->getClientWeixin()->getWxID()])
                );
                return $this->layout()->setTerminal(true);
            }


            if (Client::DOMAIN_ALL == $postData[WeixinClientForm::FIELD_DOMAIN]) {
                $postData[WeixinClientForm::FIELD_DOMAIN] = '*';
            }
            if (Client::IP_ALL == $postData[WeixinClientForm::FIELD_IP]) {
                $postData[WeixinClientForm::FIELD_IP] = '*';
            }
            $form->setData($postData);
        }


        $this->addResultData('apiList', $apiList);
        $this->addResultData('client', $client);
        $this->addResultData('form', $form);
        $this->addResultData('activeID', WeixinController::class);

    }


    /**
     * Add a client
     */
    public function addAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);

        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $apiList = ApiController::OpenedApi();

        $form = new WeixinClientForm();
        if($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            if ('*' == $postData[WeixinClientForm::FIELD_DOMAIN]) {
                $postData[WeixinClientForm::FIELD_DOMAIN] = Client::DOMAIN_ALL;
            }
            if ('*' == $postData[WeixinClientForm::FIELD_IP]) {
                $postData[WeixinClientForm::FIELD_IP] = Client::IP_ALL;
            }
            $form->setData($postData);
            if ($form->isValid()) {
                $data = $form->getData();

                $selectedApis = $data[WeixinClientForm::FIELD_API];
                if (empty($selectedApis)) {
                    $selectedApis = '';
                } else {
                    $selectedApis = json_encode($selectedApis);
                }

                $client = new Client();
                $client->setClientID(Uuid::uuid1()->toString());
                $client->setClientName($data[WeixinClientForm::FIELD_NAME]);
                $client->setClientDomain($data[WeixinClientForm::FIELD_DOMAIN]);
                $client->setClientIp($data[WeixinClientForm::FIELD_IP]);
                $client->setClientStart(new \DateTime($data[WeixinClientForm::FIELD_START]));
                $client->setClientExpired(new \DateTime($data[WeixinClientForm::FIELD_EXPIRED]));
                $client->setClientCreated(new \DateTime());
                $client->setClientApi($selectedApis);
                $client->setClientWeixin($weixin);

                $weixinManager->saveModifiedClient($client);

                $this->go(
                    '客户端已创建',
                    '客户端: ' . $client->getClientName() . ' 已经创建成功!',
                    $this->url()->fromRoute('admin/weixin-client', ['action' => 'index', 'key' => $wxID])
                );
                return $this->layout()->setTerminal(true);
            }


            if (Client::DOMAIN_ALL == $postData[WeixinClientForm::FIELD_DOMAIN]) {
                $postData[WeixinClientForm::FIELD_DOMAIN] = '*';
            }
            if (Client::IP_ALL == $postData[WeixinClientForm::FIELD_IP]) {
                $postData[WeixinClientForm::FIELD_IP] = '*';
            }
            $form->setData($postData);
        }

        $this->addResultData('apiList', $apiList);
        $this->addResultData('weixin', $weixin);
        $this->addResultData('form', $form);
        $this->addResultData('activeID', WeixinController::class);

    }

    /**
     * Download api document
     */
    public function documentAction()
    {
        $clientID = $this->params()->fromRoute('key', '');
        $wxManager = $this->appWeixinManager();
        $client = $wxManager->getClient($clientID);

        if (! $client instanceof Client) {
            throw new InvalidArgumentException('Invalid client identity');
        }

        $wxID = $client->getClientWeixin()->getWxID();
        $host = $this->appConfig()->get('weixin.api.domain', '');
        if (empty($host)) {
            $host = $this->appServer()->domain();
        }

        $allowedApis = json_decode($client->getClientApi());
        $openedApis = ApiController::OpenedApi();

        $apiItems = [];
        foreach ($openedApis as $apiAction => $apiName) {
            if (in_array($apiAction, $allowedApis)) {
                $suffix = '.json';
                if ('oauth' == $apiAction) {
                    $suffix = '.html';
                }
                $path = $this->url()->fromRoute('weixin/api', ['action' => $apiAction, 'wx' => $wxID, 'client' => $clientID, 'suffix' => $suffix]);

                if ('oauth' == $apiAction) {
                    $path .= "?type=(base 或 userinfo)&url=urlencode('授权回调URL')";
                }
                if('js-sign' == $apiAction) {
                    $path .= "?url=urlencode('需签名的URL')";
                }
                if('userinfo' == $apiAction || 'member' == $apiAction) {
                    $path .= "?openid=OPENID";
                }

                $apiItems[] = [
                    'title' => $apiName,
                    'url' => $host . $path,
                ];
            }
        }


        $excel = new \PHPExcel();
        $excel->getProperties()->setCreator($_SERVER['HTTP_HOST']);
        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', '接口名')
            ->setCellValue('B1', '接口地址');
        $start = 2;
        foreach($apiItems as $api) {
            $titleCell = 'A' . $start;
            $valueCell = 'B' . $start;
            $start++;
            $excel->setActiveSheetIndex(0)
                ->setCellValue($titleCell, $api['title'])
                ->setCellValue($valueCell, $api['url']);
        }

        $excel->getActiveSheet()->setTitle('微信公众号接口列表');
        $excel->setActiveSheetIndex(0);

        $excelWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');

        $filename = 'Client_' . $clientID . '_Api_'  . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $excelWriter->save('php://output');

        return $response = $this->getResponse();

        //$headers = $response->getHeaders();
        //$headers->addHeaderLine('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //$headers->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"');
        //$excelWriter->save('php://output');
        //return $response;
    }

}
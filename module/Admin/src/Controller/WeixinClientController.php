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

                $weixinManager->saveModifiedWeixinClient($weixin, $client);

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

}
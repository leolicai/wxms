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
use Admin\Form\WeixinQRCodeForm;
use Ramsey\Uuid\Uuid;
use Weixin\Entity\QRCode;
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


    /**
     * Export a QRCode
     */
    public function exportAction()
    {
        $key = $this->params()->fromRoute('key', '');
        $keys = explode('_', $key);

        if (count($keys) != 2) {
            throw new InvalidArgumentException('Invalid url request');
        }

        $qrcodeID = array_shift($keys);
        $type = array_shift($keys);

        $wxManager = $this->appWeixinManager();
        $qrcode = $wxManager->getQRCode($qrcodeID);

        if (! $qrcode instanceof QRCode) {
            throw new InvalidArgumentException('Invalid QRCode identity');
        }

        echo 'download';

        return $this->getResponse();

    }


    /**
     * Delete a QRCode
     */
    public function deleteAction()
    {
        $qrcodeID = $this->params()->fromRoute('key', '');
        $wxManager = $this->appWeixinManager();

        $qrcode = $wxManager->getQRCode($qrcodeID);

        if (! $qrcode instanceof QRCode) {
            throw new InvalidArgumentException('Invalid QRCode identity');
        }

        $wxID = $qrcode->getQrcodeWeixin()->getWxID();
        $wxManager->removeQRCode($qrcode);

        $this->go(
            '二维码已经删除',
            '二维码已经删除, 相关的二维码信息已失效!',
            $this->url()->fromRoute('admin/weixin-qrcode', ['action' => 'index', 'key' => $wxID])
        );

        return $this->layout()->setTerminal(true);
    }


    /**
     * Add a QRCode
     */
    public function addAction()
    {

        $wxID = (int)$this->params()->fromRoute('key', 0);

        $wxManager = $this->appWeixinManager();
        $wx = $wxManager->getWeixin($wxID);

        if (! $wx instanceof Weixin) {
            throw new InvalidArgumentException('Invalid wx identity');
        }

        $form = new WeixinQRCodeForm();
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                if($data[WeixinQRCodeForm::FIELD_EXPIRED] < 30) {
                    $data[WeixinQRCodeForm::FIELD_EXPIRED] = 30;
                }

                $qrcode = new QRCode();
                $qrcode->setQrcodeID(Uuid::uuid1()->toString());
                $qrcode->setQrcodeName($data[WeixinQRCodeForm::FIELD_NAME]);
                $qrcode->setQrcodeType($data[WeixinQRCodeForm::FIELD_TYPE]);
                $qrcode->setQrcodeScene($data[WeixinQRCodeForm::FIELD_SCENE]);
                $qrcode->setQrcodeExpired($data[WeixinQRCodeForm::FIELD_EXPIRED]);
                $qrcode->setQrcodeCreated(new \DateTime());
                $qrcode->setQrcodeWeixin($wx);

                try {
                    $res = $this->appWeixinService()->qrCodeCreate($wx, $qrcode);
                    $qrcode->setQrcodeWxUrl($res['url']);
                    $qrcode->setQrcodeExpired(time() + $res['expire_seconds']);

                    $wxManager->saveModifiedQRCode($qrcode);

                } catch (\Weixin\Exception\InvalidArgumentException $e) {
                    $this->appLogger()->exception($e);
                }

                $this->go(
                    '二维码已创建',
                    '二维码: ' . $qrcode->getQrcodeName() . ' 已经创建成功!',
                    $this->url()->fromRoute('admin/weixin-qrcode', ['action' => 'index', 'key' => $wxID])
                );
                return $this->layout()->setTerminal(true);
            }
        }

        $this->addResultData('weixin', $wx);
        $this->addResultData('form', $form);
        $this->addResultData('activeID', WeixinController::class);

    }

}
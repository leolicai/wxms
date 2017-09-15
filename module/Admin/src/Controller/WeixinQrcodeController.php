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
                $qrcode->setQrcodeCreated(new \DateTime());
                $qrcode->setQrcodeWeixin($wx);

                $res = $this->appWeixinService()->qrCodeCreate($wx, $qrcode);
                $qrcode->setQrcodeWxUrl($res['url']);
                $qrcode->setQrcodeExpired(time() + $res['expire_seconds']);

                $wxManager->saveModifiedQRCode($qrcode);

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
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
use Weixin\Controller\ApiController;
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
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
            }
        }

        $this->addResultData('apiList', $apiList);
        $this->addResultData('weixin', $weixin);
        $this->addResultData('form', $form);
        $this->addResultData('activeID', WeixinController::class);

    }

}
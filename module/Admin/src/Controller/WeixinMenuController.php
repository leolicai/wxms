<?php
/**
 * WeixinMenuController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/12
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\InvalidArgumentException;
use Weixin\Entity\Weixin;

class WeixinMenuController extends AdminBaseController
{

    /**
     * Page for menu list
     */
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

    /**
     * Delete a menu
     */
    public function deleteAction()
    {
        //todo
    }

    /**
     * Import menu from weixin
     */
    public function importAction()
    {
        //todo
    }

    /**
     * Async a menu to weixin
     */
    public function asyncAction()
    {
        //todo
    }

    /**
     * Add a menu
     */
    public function addAction()
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

}
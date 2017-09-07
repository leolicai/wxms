<?php
/**
 * DashboardController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\Controller;


class DashboardController extends AdminBaseController
{

    public function indexAction()
    {
        $this->addResultData('activeID', __METHOD__);
    }

}
<?php
/**
 * AdminBaseController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Service\AdminerManager;
use Admin\Service\AuthService;
use Application\Controller\AppBaseController;


/**
 * Class AdminBaseController
 * @package Admin\Controller
 */
class AdminBaseController extends AppBaseController
{

    /**
     * @return AdminerManager
     */
    protected function appAdminAdminerManager()
    {
        return $this->appServiceManager(AdminerManager::class);
    }

    /**
     * @return AuthService
     */
    protected function appAdminAuthService()
    {
        return $this->appServiceManager(AuthService::class);
    }


    /**
     * Display information for option
     *
     * @param string $topic
     * @param string $content
     * @param string $href
     * @param string $title
     * @param int $delay
     * @return mixed
     */
    protected function go($topic = 'Message', $content = '...', $href = '', $title = '返回', $delay = 3)
    {

        $this->addResultData('topic', $topic);
        $this->addResultData('content', $content);
        $this->addResultData('url', $href);
        $this->addResultData('title', $title);
        $this->addResultData('delay', $delay);

        return $this->forward()->dispatch(
            IndexController::class,
            ['controller' => IndexController::class, 'action' => 'message']
        );
    }

}
<?php
/**
 * ProfileController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\Adminer;
use Admin\Exception\RuntimeException;
use Admin\Form\UpdatePasswordForm;
use Admin\Form\UpdateProfileForm;


class ProfileController extends AdminBaseController
{

    /**
     * Display administrator summery profile
     */
    public function indexAction()
    {
        $authService = $this->appAdminAuthService();
        $adminerManager = $this->appAdminAdminerManager();

        $adminer = $adminerManager->getAdminerByID($authService->getIdentity());
        if (!$adminer instanceof Adminer) {
            throw new RuntimeException('Invalid login administrator', 1001);
        }

        $this->addResultData('adminer', $adminer);
    }


    /**
     * 个人密码
     *
     */
    public function passwordAction()
    {

        $authService = $this->appAdminAuthService();
        $adminerManager = $this->appAdminAdminerManager();

        $adminer = $adminerManager->getAdminerByID($authService->getIdentity());
        if (!$adminer instanceof Adminer) {
            throw new RuntimeException('Invalid login administrator', 1001);
        }

        $form = new UpdatePasswordForm($adminerManager, $authService);
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $adminer->setAdminPasswd($data[UpdatePasswordForm::FIELD_NEW_PASSWORD]);

                $adminerManager->saveModifiedAdminer($adminer);

                $this->go(
                    '密码已更新',
                    '您的密码已经更新, 请在下次使用新的密码登入!',
                    $this->url()->fromRoute('admin/profile', ['suffix' => '.html'])
                );

                return $this->layout()->setTerminal(true);
            }
        }

        $this->addResultData('form', $form);
    }


    /**
     * 个人资料
     */
    public function updateAction()
    {
        $authService = $this->appAdminAuthService();
        $adminerManager = $this->appAdminAdminerManager();

        $adminer = $adminerManager->getAdminerByID($authService->getIdentity());
        if (!$adminer instanceof Adminer) {
            throw new RuntimeException('Invalid login administrator', 1001);
        }

        $form = new UpdateProfileForm();
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $adminer->setAdminName($data[UpdateProfileForm::FIELD_NAME]);

                $adminerManager->saveModifiedAdminer($adminer);

                $this->go(
                    '资料已更新',
                    '您的个人资料已经更新!',
                    $this->url()->fromRoute('admin/profile', ['suffix' => '.html'])
                );

                return $this->layout()->setTerminal(true);
            }
        }

        $adminer->setAdminPasswd('');
        $this->addResultData('form', $form);
        $this->addResultData('adminer', $adminer);
    }


}
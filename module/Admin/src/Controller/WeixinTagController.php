<?php
/**
 * WeixinTagController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/12
 * Version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\InvalidArgumentException;
use Admin\Form\WeixinTagForm;
use Ramsey\Uuid\Uuid;
use Weixin\Entity\Tag;
use Weixin\Entity\Weixin;


class WeixinTagController extends AdminBaseController
{

    /**
     * Page for tag list
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
     * Import tags from weixin platform
     */
    public function importAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wxID = (int)$this->params()->fromRoute('key', 0);
        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);
        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $localTags = $weixin->getWxTags();

        $weixinService = $this->appWeixinService();
        $tags = $weixinService->tagsExport($weixin);
        $newTags = [];
        foreach (@(array)$tags['tags'] as $item) {
            $newTags[$item['id']] = $item;
        }

        foreach ($localTags as  $tag) {
            if (! array_key_exists($tag->getTagID(), $newTags)) {
                $localTags->removeElement($tag);
                $weixinManager->getEntityManager()->remove($tag);
            } else {
                $newTagName = $newTags[$tag->getTagID()]['name'];
                $newTagCount = $newTags[$tag->getTagID()]['count'];
                if ($tag->getTagName() != $newTagName || $tag->getTagCount() != $newTagCount) {
                    $tag->setTagName($newTagName);
                    $tag->setTagCount($newTagCount);
                    $weixinManager->getEntityManager()->persist($tag);
                }
                unset($newTags[$tag->getTagID()]);
            }
        }
        $weixinManager->getEntityManager()->flush();

        if (!empty($newTags)) {
            foreach ($newTags as $newTag) {
                $tag = new Tag();
                $tag->setId(Uuid::uuid1()->toString());
                $tag->setTagID($newTag['id']);
                $tag->setTagName($newTag['name']);
                $tag->setTagCount($newTag['count']);
                $tag->setTagWeixin($weixin);
                $weixinManager->getEntityManager()->persist($tag);
            }
            $weixinManager->getEntityManager()->flush();
        }

    }


    /**
     * Remove tag
     * Async local and weixin platform
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('key', '');

        $weixinManager = $this->appWeixinManager();
        $tag = $weixinManager->getTag($id);
        if (! $tag instanceof Tag) {
            throw new InvalidArgumentException('Invalid tag ID');
        }

        $weixinService = $this->appWeixinService();
        $weixinService->tagDelete($tag->getTagWeixin(), $tag);

        $weixinManager->removeTag($tag);

        $this->go(
            'Tag 已删除',
            'Tag 已经删除. 已同步删除微信公众号平台该 Tag.',
            $this->url()->fromRoute('admin/weixin-tag', ['action' => 'index', 'key' => $tag->getTagWeixin()->getWxID()])
        );
        return $this->layout()->setTerminal(true);

    }

    /**
     * Add a tag
     * Async local and weixin platform
     */
    public function addAction()
    {
        $wxID = (int)$this->params()->fromRoute('key', 0);

        $weixinManager = $this->appWeixinManager();
        $weixin = $weixinManager->getWeixin($wxID);
        if (! $weixin instanceof Weixin) {
            throw new InvalidArgumentException('Invalid weixin identity');
        }

        $form = new WeixinTagForm();
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $weixinService = $this->appWeixinService();
                $res = $weixinService->tagCreate($weixin, $data[WeixinTagForm::FIELD_NAME]);

                $tag = new Tag();
                $tag->setId(Uuid::uuid1()->toString());
                $tag->setTagID($res['tag']['id']);
                $tag->setTagName($res['tag']['name']);
                $tag->setTagWeixin($weixin);

                $weixinManager->saveModifiedTag($tag);

                $this->go(
                    'Tag 已创建',
                    'Tag 已经创建完成, 并且已经同步到微信公众号平台.',
                    $this->url()->fromRoute('admin/weixin-tag', ['action' => 'index', 'key' => $weixin->getWxID()])
                );
                return $this->layout()->setTerminal(true);
            }
        }

        $this->addResultData('form', $form);
        $this->addResultData('weixin', $weixin);
        $this->addResultData('activeID', WeixinController::class);
    }

    /**
     * Edit a tag
     * Async local and weixin platform
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('key', '');

        $weixinManager = $this->appWeixinManager();
        $tag = $weixinManager->getTag($id);
        if (! $tag instanceof Tag) {
            throw new InvalidArgumentException('Invalid tag ID');
        }

        $form = new WeixinTagForm();
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $newName = $data[WeixinTagForm::FIELD_NAME];
                if ($tag->getTagName() != $newName) {
                    $tag->setTagName($newName);
                    $weixinService = $this->appWeixinService();
                    $weixinService->tagUpdate($tag->getTagWeixin(), $tag);
                    $weixinManager->saveModifiedTag($tag);
                }
                $this->go(
                    'Tag 已更新',
                    'Tag 已经更新. 并且已经同步到微信公众号平台.',
                    $this->url()->fromRoute('admin/weixin-tag', ['action' => 'index', 'key' => $tag->getTagWeixin()->getWxID()])
                );
                return $this->layout()->setTerminal(true);
            }
        }

        $this->addResultData('form', $form);
        $this->addResultData('tag', $tag);
        $this->addResultData('activeID', WeixinController::class);
    }

}
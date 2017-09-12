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
        $tags = $weixinService->getTags($wxID);
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
        //todo
    }

    /**
     * Add a tag
     * Async local and weixin platform
     */
    public function addAction()
    {
        //todo
    }

    /**
     * Edit a tag
     * Async local and weixin platform
     */
    public function editAction()
    {
        //todo
    }

}
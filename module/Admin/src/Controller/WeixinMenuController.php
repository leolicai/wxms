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
use Ramsey\Uuid\Uuid;
use Weixin\Entity\Menu;
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
        $this->setResultType(self::RESPONSE_JSON);

        $id = $this->params()->fromRoute('key', '');

        $wxManager = $this->appWeixinManager();
        $menu = $wxManager->getMenu($id);
        if (! $menu instanceof Menu) {
            throw new InvalidArgumentException('Invalid wx menu id');
        }

        $wxManager->removeMenu($menu);
    }


    /**
     * Active or retired a menu
     */
    public function activeAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $id = $this->params()->fromRoute('key', '');

        $wxManager = $this->appWeixinManager();
        $menu = $wxManager->getMenu($id);
        if (! $menu instanceof Menu) {
            throw new InvalidArgumentException('Invalid wx menu id');
        }

        $status = $menu->getMenuStatus();
        if (Menu::STATUS_ACTIVATED == $status) {
            $menu->setMenuStatus(Menu::STATUS_BACKUP);
        } else {
            $menu->setMenuStatus(Menu::STATUS_ACTIVATED);
        }

        $wxManager->saveModifiedMenu($menu);
    }


    /**
     * Trash wx platform menus
     */
    public function trashAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wxID = (int)$this->params()->fromRoute('key', 0);
        $wx = $this->appWeixinManager()->getWeixin($wxID);
        if (! $wx instanceof Weixin) {
            throw new InvalidArgumentException('Invalid wx identity');
        }

        $this->appWeixinService()->menuTrashed($wx);
    }

    /**
     * Import menu from weixin
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

        $weixinService = $this->appWeixinService();
        $menus = $weixinService->menusExport($weixin);
        if(!empty($menus)) {
            $i = 0;
            foreach ($menus as $key => $menu) {
                $name = $i < 1 ? '自定义菜单' : '个性化菜单-' . $i;
                $type = $i < 1 ? Menu::TYPE_DEFAULT : Menu::TYPE_ADDITIONAL;
                $i++;

                $menuEntity = new Menu();
                $menuEntity->setId(Uuid::uuid1()->toString());
                $menuEntity->setMenuCreated(new \DateTime());
                $menuEntity->setMenuName($name);
                $menuEntity->setMenuType($type);
                $menuEntity->setMenuStatus(Menu::STATUS_ACTIVATED);
                $menuEntity->setMenuData(json_encode($menu, JSON_UNESCAPED_UNICODE));
                $menuEntity->setMenuWeixin($weixin);
                $weixinManager->saveModifiedMenu($menuEntity);
            }
        }
    }


    /**
     * Async local activated menus for wx platform
     */
    public function asyncAction()
    {
        $this->setResultType(self::RESPONSE_JSON);

        $wxID = (int)$this->params()->fromRoute('key', 0);

        $wxManager = $this->appWeixinManager();
        $wx = $wxManager->getWeixin($wxID);
        if (! $wx instanceof Weixin) {
            throw new InvalidArgumentException('Invalid wx id');
        }

        $menus = $wx->getWxMenus();

        $menuDefault = null;
        $menuCond1 = null;
        $menuCond2 = null;
        $menuCond3 = null;

        foreach ($menus as $menu) {
            if ($menu->getMenuType() == Menu::TYPE_DEFAULT) {
                $menuDefault = $menu;
            } else {
                if (($menuCond1 instanceof Menu) && ($menuCond2 instanceof Menu) && ($menuCond3 instanceof Menu)) {
                    continue;
                }
                if (! $menuCond1 instanceof Menu) {
                    $menuCond1 = $menu;
                }
                if (! $menuCond2 instanceof Menu) {
                    $menuCond2 = $menu;
                }
                if (! $menuCond3 instanceof Menu) {
                    $menuCond3 = $menu;
                }
            }
        }

        $wxService = $this->appWeixinService();
        $wxService->menuTrashed($wx);

        if ($menuDefault instanceof Menu) {
            $wxService->menuCreateDefault($wx, $menuDefault);
        }

        if ($menuCond1 instanceof Menu) {
            $wxService->menuCreateConditional($wx, $menuCond1);
        }
        if ($menuCond2 instanceof Menu) {
            $wxService->menuCreateConditional($wx, $menuCond2);
        }
        if ($menuCond3 instanceof Menu) {
            $wxService->menuCreateConditional($wx, $menuCond3);
        }
    }


    /**
     * Edit a menu
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('key', '');

        $weixinManager = $this->appWeixinManager();
        $menuEntity = $weixinManager->getMenu($id);
        if (! $menuEntity instanceof Menu) {
            throw new InvalidArgumentException('Invalid weixin menu id');
        }

        $allowedMenuTypes = $this->appConfig()->get('weixin.menu.type');
        if($this->getRequest()->isPost()) {

            $menuTitle = strip_tags($this->params()->fromPost('menuTitle'));
            if (empty($menuTitle)) {
                $this->go(
                    '需要设置菜单名称',
                    '每套菜单需要设定一个名字方便管理!',
                    $this->url()->fromRoute('admin/weixin-menu', ['action' => 'edit', 'key' => id])
                );
                return $this->layout()->setTerminal(true);
            }

            $menuBar = new \stdClass();
            $menuBar->button = [];

            $topMenuNames = $this->params()->fromPost('menuName', []); // Top menu names array
            $topMenuTypes = $this->params()->fromPost('menuType', []); // Top menu types array
            $topMenuValues = $this->params()->fromPost('menuValue', []); // Top menu values array
            foreach ($topMenuNames as $topIndex => $topMenuName) {
                if (!isset($topMenuTypes[$topIndex]) || !isset($topMenuValues[$topIndex]) || empty($topMenuName)) { continue; } // Make sure the menu has a selected type
                $topMenuType = $topMenuTypes[$topIndex];
                $topMenuValue = $topMenuValues[$topIndex];

                if ('parent' != $topMenuType) { // No children menus
                    if (!array_key_exists($topMenuType, $allowedMenuTypes)) { continue; } // Check the type is valid.

                    if (empty($topMenuValue)) { $topMenuValue = 'menu_' . $topIndex . '_0'; }

                    $menu = $this->buildMenuObject($topMenuName, $topMenuType, $topMenuValue);
                    if ($menu instanceof \stdClass) {
                        $menuBar->button[] = $menu;
                    }

                }
                else { // sub menus
                    $subMenuNames = $this->params()->fromPost('subMenuName', []);
                    $subMenuTypes = $this->params()->fromPost('subMenuType', []);
                    $subMenuValues = $this->params()->fromPost('subMenuValue', []);
                    if (isset($subMenuNames[$topIndex]) && isset($subMenuTypes[$topIndex]) && isset($subMenuValues[$topIndex])) {

                        $menu = new \stdClass();
                        $menu->name = $topMenuName;
                        $menu->sub_button = [];

                        $subNames = $subMenuNames[$topIndex];
                        $subTypes = $subMenuTypes[$topIndex];
                        $subValues = $subMenuValues[$topIndex];

                        foreach ($subNames as $subIndex => $subName) {
                            if (!isset($subTypes[$subIndex]) || !isset($subValues[$subIndex])) { continue; }
                            $subType = $subTypes[$subIndex];
                            $subValue = $subValues[$subIndex];
                            if (!array_key_exists($subType, $allowedMenuTypes)) { continue; } // Check the type is valid.

                            if (empty($subValue)) { $subValue = 'menu_' . $topIndex . '_' . $subIndex; }

                            $subMenu = $this->buildMenuObject($subName, $subType, $subValue);
                            if ($subMenu instanceof \stdClass) {
                                $menu->sub_button[] = $subMenu;
                            }
                        }

                        $menuBar->button[] = $menu;
                    }
                }
            }

            if (count($menuBar->button) < 1) {
                $this->go(
                    '菜单为空',
                    '请创建一个至少包含一个菜单的菜单配置单!',
                    $this->url()->fromRoute('admin/weixin-menu', ['action' => 'edit', 'key' => $id])
                );
                return $this->layout()->setTerminal(true);
            }

            $menuCategory = $this->params()->fromPost('menuCategory');
            if (Menu::TYPE_ADDITIONAL == $menuCategory) {
                $condObj = $this->getMatchRuleObj();
                if ($condObj instanceof \stdClass) {
                    $menuBar->matchrule = $condObj;
                } else {
                    $menuCategory = Menu::TYPE_DEFAULT;
                }
            } else {
                $menuCategory = Menu::TYPE_DEFAULT;
            }

            $menuEntity->setMenuName($menuTitle);
            $menuEntity->setMenuData(json_encode($menuBar, JSON_UNESCAPED_UNICODE));
            $menuEntity->setMenuType($menuCategory);
            $weixinManager->saveModifiedMenu($menuEntity);

            $this->go(
                '菜单已保存',
                '微信菜单: ' . $menuTitle . ' 已经修改完成!',
                $this->url()->fromRoute('admin/weixin-menu', ['action' => 'index', 'key' => $menuEntity->getMenuWeixin()->getWxID()])
            );
            return $this->layout()->setTerminal(true);
        }


        $this->addResultData('type', $allowedMenuTypes);
        $this->addResultData('language', $this->appConfig()->get('weixin.menu.language'));
        $this->addResultData('menu', $menuEntity);
        $this->addResultData('activeID', WeixinController::class);
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

        $allowedMenuTypes = $this->appConfig()->get('weixin.menu.type');
        if($this->getRequest()->isPost()) {

            $menuTitle = strip_tags($this->params()->fromPost('menuTitle'));
            if (empty($menuTitle)) {
                $this->go(
                    '需要设置菜单名称',
                    '每套菜单需要设定一个名字方便管理!',
                    $this->url()->fromRoute('admin/weixin-menu', ['action' => 'add', 'key' => $wxID])
                );
                return $this->layout()->setTerminal(true);
            }

            $menuBar = new \stdClass();
            $menuBar->button = [];

            $topMenuNames = $this->params()->fromPost('menuName', []); // Top menu names array
            $topMenuTypes = $this->params()->fromPost('menuType', []); // Top menu types array
            $topMenuValues = $this->params()->fromPost('menuValue', []); // Top menu values array
            foreach ($topMenuNames as $topIndex => $topMenuName) {
                if (!isset($topMenuTypes[$topIndex]) || !isset($topMenuValues[$topIndex]) || empty($topMenuName)) { continue; } // Make sure the menu has a selected type
                $topMenuType = $topMenuTypes[$topIndex];
                $topMenuValue = $topMenuValues[$topIndex];

                if ('parent' != $topMenuType) { // No children menus
                    if (!array_key_exists($topMenuType, $allowedMenuTypes)) { continue; } // Check the type is valid.

                    if (empty($topMenuValue)) {
                        $topMenuValue = 'menu_' . $topIndex . '_0';
                    }

                    $menu = $this->buildMenuObject($topMenuName, $topMenuType, $topMenuValue);
                    if ($menu instanceof \stdClass) {
                        $menuBar->button[] = $menu;
                    }
                }
                else { // sub menus
                    $subMenuNames = $this->params()->fromPost('subMenuName', []);
                    $subMenuTypes = $this->params()->fromPost('subMenuType', []);
                    $subMenuValues = $this->params()->fromPost('subMenuValue', []);
                    if (isset($subMenuNames[$topIndex]) && isset($subMenuTypes[$topIndex]) && isset($subMenuValues[$topIndex])) {

                        $menu = new \stdClass();
                        $menu->name = $topMenuName;
                        $menu->sub_button = [];

                        $subNames = $subMenuNames[$topIndex];
                        $subTypes = $subMenuTypes[$topIndex];
                        $subValues = $subMenuValues[$topIndex];

                        foreach ($subNames as $subIndex => $subName) {
                            if (!isset($subTypes[$subIndex]) || !isset($subValues[$subIndex])) { continue; }
                            $subType = $subTypes[$subIndex];
                            $subValue = $subValues[$subIndex];
                            if (!array_key_exists($subType, $allowedMenuTypes)) { continue; } // Check the type is valid.

                            if (empty($subValue)) {
                                $subValue = 'menu_' . $topIndex . '_' . $subIndex;
                            }

                            $subMenu = $this->buildMenuObject($subName, $subType, $subValue);
                            if ($subMenu instanceof \stdClass) {
                                $menu->sub_button[] = $subMenu;
                            }
                        }

                        $menuBar->button[] = $menu;
                    }
                }
            }

            if (count($menuBar->button) < 1) {
                $this->go(
                    '菜单为空',
                    '请创建一个至少包含一个菜单的菜单配置单!',
                    $this->url()->fromRoute('admin/weixin-menu', ['action' => 'add', 'key' => $wxID])
                );
                return $this->layout()->setTerminal(true);
            }

            $menuCategory = $this->params()->fromPost('menuCategory');
            if (Menu::TYPE_ADDITIONAL == $menuCategory) {
                $condObj = $this->getMatchRuleObj();
                if ($condObj instanceof \stdClass) {
                    $menuBar->matchrule = $condObj;
                } else {
                    $menuCategory = Menu::TYPE_DEFAULT;
                }
            } else {
                $menuCategory = Menu::TYPE_DEFAULT;
            }

            $menuEntity = new Menu();
            $menuEntity->setId(Uuid::uuid1()->toString());
            $menuEntity->setMenuName($menuTitle);
            $menuEntity->setMenuData(json_encode($menuBar, JSON_UNESCAPED_UNICODE));
            $menuEntity->setMenuStatus(Menu::STATUS_BACKUP);
            $menuEntity->setMenuType($menuCategory);
            $menuEntity->setMenuCreated(new \DateTime());
            $menuEntity->setMenuWeixin($weixin);

            $weixinManager->saveModifiedMenu($menuEntity);

            $this->go(
                '菜单已创建',
                '微信菜单: ' . $menuTitle . ' 已经创建!',
                $this->url()->fromRoute('admin/weixin-menu', ['action' => 'index', 'key' => $wxID])
            );
            return $this->layout()->setTerminal(true);
        }

        $this->addResultData('type', $allowedMenuTypes);
        $this->addResultData('language', $this->appConfig()->get('weixin.menu.language'));
        $this->addResultData('weixin', $weixin);
        $this->addResultData('activeID', WeixinController::class);
    }


    /**
     * @param string $name
     * @param string $type
     * @param string $value
     * @return \stdClass | bool
     */
    private function buildMenuObject($name, $type, $value)
    {
        $menu = new \stdClass();
        $menu->name = $name; // $name max length: 16 Bytes = 16/3 UTF-8 chars
        $menu->type = $type;
        if(in_array($type, ['media_id', 'view_limited'])) {
            $menu->media_id = $value;
        } else if('view' == $type) {
            $menu->url = $value; // $value max length: 1024 Byte = 1024/3 UTF-8 chars
        } else if('miniprogram' == $type) { //Value example: http://mp.weixin.qq.com,wx286b93c14bbf93aa,pages/lunar/index

            $_values = explode(',', $value);

            if (count($_values) != 3) {
                return false;
            }

            $_url = array_shift($_values);
            $_appid = array_shift($_values);
            $_path = array_shift($_values);

            $menu->url = str_replace(' ', '', trim($_url));
            $menu->appid = str_replace(' ', '', trim($_appid));
            $menu->pagepath = str_replace(' ', '', trim($_path));
        } else {
            $menu->key = $value; // $value max length: 128 Byte = 128/3 UTF-8 chars
        }

        return $menu;
    }


    /**
     * @return bool|\stdClass
     */
    private function getMatchRuleObj()
    {
        $menuForSex = (string)$this->params()->fromPost('menuForSex', '');
        $menuForPlatform = (string)$this->params()->fromPost('menuForPlatform', '');
        $menuForTag = (string)$this->params()->fromPost('menuForTag', '');

        $menuForCountry = (string)$this->params()->fromPost('menuForCountry', '');
        $menuForProvince = (string)$this->params()->fromPost('menuForProvince', '');
        $menuForCity = (string)$this->params()->fromPost('menuForCity', '');
        $menuForLang = (string)$this->params()->fromPost('menuForLang', '');

        if (!empty($menuForSex) || !empty($menuForPlatform) || !empty($menuForTag) ||
            !empty($menuForCountry) || !empty($menuForProvince) || !empty($menuForCity) || !empty($menuForLang)) {
            $condObj = new \stdClass();
            $condObj->tag_id = $menuForTag;
            $condObj->client_platform_type = $menuForPlatform;
            $condObj->sex = $menuForSex;
            $condObj->country = $menuForCountry;
            $condObj->province = $menuForProvince;
            $condObj->city = $menuForCity;
            $condObj->language = $menuForLang;

            return $condObj;
        }
        return false;
    }


}
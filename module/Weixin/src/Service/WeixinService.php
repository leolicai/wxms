<?php
/**
 * WeixinService.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Service;


class WeixinService
{

    /**
     * @var WeixinManager
     */
    private $weixinManager;

    public function __construct(WeixinManager $weixinManager)
    {
        $this->weixinManager = $weixinManager;
    }

}
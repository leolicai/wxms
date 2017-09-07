<?php
/**
 * Module.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin;


class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
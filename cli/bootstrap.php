<?php
/**
 * cli/bootstrap.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;



// Autoload 配置
require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $config = ArrayUtils::merge($config, require __DIR__ . '/../config/development.config.php');
}


$app = Application::init($config);
$config = $app->getConfig();
if(!isset($config['doctrine']['connection']['orm_default']['params'])) {
    echo 'No database configuration!';
    exit(1);
}

$dbConfig = $config['doctrine']['connection']['orm_default']['params'];



$isDevMode = true;
$emConfig = Setup::createAnnotationMetadataConfiguration(
    [
        'AdminEntity' => __DIR__ . "/../module/Admin/src/Entity",
    ],
    $isDevMode,
    null,
    null,
    false
);
$emConfig->addEntityNamespace('WeChatEntity', 'WeChat\Entity');

// 创建实体管理器
$entityManager = EntityManager::create($dbConfig, $emConfig);

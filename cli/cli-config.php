<?php
/**
 * cli/cli-config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

require_once "bootstrap.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);

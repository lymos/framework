<?php
defined('root_path') or define('root_path', dirname(dirname(__FILE__)).'/');
defined('debug') or define('debug', true);

$global_config = require_once root_path . 'Config/Config.php';
require_once root_path . 'Common/Common.php';
// require_once root_path . 'Core/Init.php';
require_once root_path . 'Core/AutoLoad/AutoLoad.php';
?>

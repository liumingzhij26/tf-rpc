<?php
/***************************************************************************
 *
 * Copyright (c) 2019 liumingzhi, Inc. All Rights Reserved
 *
 **************************************************************************
 *
 * @file start.php
 * @author liumingzhi(liumingzhij26@gmail.com)
 * @date 2019-11-14 18:39:00
 *
 **/


ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

!defined('APP_PATH') && define('APP_PATH', dirname(__DIR__, 1));
!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

require APP_PATH . '/vendor/autoload.php';


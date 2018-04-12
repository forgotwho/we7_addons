<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

!defined('CARD_JPG_PATH') && define('CARD_JPG_PATH', IA_ROOT . '/addons/card_jpg');
!defined('CARD_JPG_INC') && define('CARD_JPG_INC', CARD_JPG_PATH . '/inc');
!defined('CARD_JPG_PATH') && define('CARD_JPG_PATH', CARD_JPG_PATH . '/data/log/');
!defined('CARD_JPG_LIB') && define('CARD_JPG_LIB', CARD_JPG_PATH . '/lib');
define('EVAL_DEBUG', false);
require_once CARD_JPG_INC . '/functions.php';
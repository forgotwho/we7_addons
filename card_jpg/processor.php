<?php

defined('IN_IA') or exit('Access Denied');

require_once IA_ROOT . '/addons/card_jpg/defines.php';

class Card_jpgModuleProcessor extends WeModuleProcessor {
    public function respond() {
        global $_W;
        //xlog('11111',TIMESTAMP,$this->message);
        $message=$this->message['content'];
        if ($message=="列表"){
            return $this->respText('您触发了we7_demo模块');
        }
    }

}


?>
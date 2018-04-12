<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

require IA_ROOT . '/addons/card_jpg/defines.php';

class Card_jpgModuleReceiver extends WeModuleReceiver {
    public function receive() {
        global $_W;
        $type = $this->message['type'];
        //这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微擎文档来编写你的代码

        $type = $this->message['type'];

        $s = '==== message ==== ' . PHP_EOL;
        foreach ($this->message as $k => $v) {
            $s .= "{$k} : {$v}" . PHP_EOL;
        }

        echo $s;
    }
}
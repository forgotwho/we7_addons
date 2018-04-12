<?php
    global $_W,$_GPC;
	$isWeixin = checkweixin();
    if(!$isWeixin){
        die('<script>alert("请通过微信打开")</script>');
    }
    $op = $_GPC['op']?$_GPC['op']:'display';
    $weid = $_W['uniacid'];
	$title = '我的名片';
    if ($op=='display'){
		$shareid = $_GPC['shareid'];
		$uid = $this->createUser();
		$sharecard = null;
		if($shareid && $shareid!=$uid){
			$title = '分享的名片';
			$sharecard = pdo_fetch("SELECT * FROM " . tablename('card') . " where uid = :uid and uniacid = :uniacid", array(':uid' => $shareid,':uniacid' => $weid));
			$linkurl = $this->createMobileUrl('active',array('op'=>'display','shareid'=>$shareid));
		} else {
			$linkurl = $this->createMobileUrl('active',array('op'=>'display','shareid'=>$uid));
		}
		$card = pdo_fetch("SELECT * FROM " . tablename('card') . " where uid = :uid and uniacid = :uniacid", array(':uid' => $uid,':uniacid' => $weid));
	    $cardset = pdo_fetch("SELECT * FROM " . tablename('card_set') . " where uniacid = :uniacid", array(':uniacid' => $weid));
	    if($cardset['shareimg']){
		    $shareimg = tomedia($cardset['shareimg']);
	    } else {
		    $shareimg = $_W['siteroot'] . 'addons/card_jpg/template/image/card.png';
	    }
    }

    include $this->template('card_active');

?>
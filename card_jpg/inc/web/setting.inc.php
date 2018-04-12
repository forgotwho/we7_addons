<?php
global $_W,$_GPC;

$op=$_GPC['op']?$_GPC['op']:'display';

if ($op=='display'){
	$cardset = pdo_fetch('SELECT * FROM '.tablename('card_set').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
	if($_W['ispost']){
		$data = array(
			'sharetitle' => $_GPC['sharetitle'],
			'sharedesc' => $_GPC['sharedesc'],
			'shareimg' => $_GPC['shareimg'],
			'advertisementimg' => $_GPC['advertisementimg'],
			'advertisementurl' => $_GPC['advertisementurl'],
			'edittime' => time()
		);
		if($cardset){
			$res = pdo_update('card_set',$data,array('uniacid'=>$_W['uniacid']));
            message('更新成功',$this->createWebUrl('setting',array('op'=>'display')));
		} else {
			$data['uniacid'] = $_W['uniacid'];
			$res = pdo_insert('card_set',$data);
            message('添加成功',$this->createWebUrl('setting',array('op'=>'display')));
		}
	}
}

include $this->template('setting');

?>

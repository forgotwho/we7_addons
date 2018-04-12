<?php
global $_W,$_GPC;

$op=$_GPC['op']?$_GPC['op']:'display';

if ($op=='post'){
    $id = $_GPC['id'];
    if ($id){
        $card = pdo_fetch('SELECT * FROM '.tablename('card_user').' WHERE id=:id',array(':id'=>$id));
    }
    if($_W['ispost']){
		if($_GPC['nickname'] == ""){
			message("昵称不得为空");
		}
		if($_GPC['headimgurl'] == ""){
			message("头像不得为空");
		}
        $data['nickname']=$_GPC['nickname'];
        $data['realname']=$_GPC['realname'];
        $data['phone']=$_GPC['phone'];
		$data['sex']=$_GPC['sex']?$_GPC['sex']:1;
        $data['headimgurl']=tomedia($_GPC['headimgurl']);
        if ($id){
            $res = pdo_update('card_user',$data,array('id'=>$id));
            message('更新用户成功',$this->createWebUrl('active',array('op'=>'display')));
        }else{
			$data['uniacid'] = $_W['uniacid'];
			$data['addtime'] = TIMESTAMP;
			$res = pdo_insert('card_user',$data);
            message('添加成功',$this->createWebUrl('active',array('op'=>'display')));
        }
    }
} elseif ($op=='display'){
    $page = max(1,intval($_GPC['page']));
    $pagesize = 10;
    $list = pdo_fetchall('SELECT * FROM '.tablename('card_user').' WHERE uniacid =:uniacid ORDER BY `id` ASC LIMIT '.($page - 1) * $pagesize . "," . $pagesize,array(':uniacid'=>$_W['uniacid']));
	foreach($list as &$v){
		$card = pdo_fetch('SELECT * FROM '.tablename('card').' WHERE uid=:id and uniacid =:uniacid',array(':id'=>$v['id'],':uniacid'=>$_W['uniacid']));
		$v['pic'] = $card['pic'];
	}
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM ". tablename('card_user').' WHERE uniacid =:uniacid',array(':uniacid'=>$_W['uniacid']) );
    $pagination = pagination($total, $page,$pagesize);
} elseif ($op == 'delete'){
    $id = $_GPC['id'];
	$card = pdo_fetch('SELECT * FROM '.tablename('card').' WHERE uid=:id and uniacid=:uniacid',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
	if($card['qrcodeurl']){
		unlink($card['qrcodeurl']);
	}
	if($card['headimgurl']){
		unlink($card['headimgurl']);
	}
	if($card['logourl']){
		unlink($card['logourl']);
	}
    $res = pdo_delete('card_user',array('id'=>$id));
	pdo_delete('card',array('uid'=>$id,'uniacid'=>$_W['uniacid']));
    if ($res) {
        message('成功删除用户',$this->createWebUrl('active',array('op'=>'display')));
    }
} elseif ($op == 'card_post'){
    $id = $_GPC['id'];
	$card = pdo_fetch('SELECT * FROM '.tablename('card').' WHERE uid=:id',array(':id'=>$id));
	if($_W['ispost']){
		$data = array(
		    'uid' => $id,
			'uniacid' => $_W['uniacid'],
			'realname' => $_GPC['realname'],
			'cardname' => $_GPC['cardname'],
			'company' => $_GPC['company'],
			'position' => $_GPC['position'],
			'tel' => $_GPC['tel'],
			'mobile' => $_GPC['mobile'],
			'address' => $_GPC['address'],
			'email' => $_GPC['email'],
			'wechat' => $_GPC['wechat'],
			'model' => $_GPC['model']?$_GPC['model']:'1',
			'font'  => $_GPC['font']?$_GPC['font']:'sjt',
			'fontcolor' => $_GPC['fontcolor']?$_GPC['fontcolor']:'black',
			'colortype' => $_GPC['colortype']?$_GPC['colortype']:'white',
			'image'  => $_GPC['image'],
			'baseimage'  => $_GPC['baseimage']
		);
		if($card){
			if($data['image'] != $card['image']){
				$data['headimg'] = '';
				$data['headimgurl'] = '';
			}
			if($data['baseimage'] != $card['baseimage']){
				$data['logo'] = '';
				$data['logourl'] = '';
				if($card['logourl']){
		            unlink($card['logourl']);
	            }
			}
			if($data['model'] != $card['model']){
				$data['logo'] = '';
				$data['logourl'] = '';
				if($card['logourl']){
		            unlink($card['logourl']);
	            }
			}
			$res = pdo_update('card',$data,array('uid'=>$id));
			$text = '更新成功';
			$cid = $card['id'];
		} else {
			$res = pdo_insert('card' ,$data);
			$text = '添加成功';
			$cid = pdo_insertid();
		}
		if ($res) {
			require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
			$attachment = IA_ROOT . "/attachment/card_jpg/";
		    if(!file_exists($attachment)){
	    	    mkdir($attachment);
	        }
			$qrcode_file = IA_ROOT . "/attachment/card_jpg/qrcode/";
		    if(!file_exists($qrcode_file)){
	    	    mkdir($qrcode_file);
	        }
			$paths = IA_ROOT . "/attachment/card_jpg/qrcode/" . $_W['uniacid'] . "/";
    	    if(!file_exists($paths)){
	    	    mkdir($paths);
	        }
		    if($card['qrcode']){
		        unlink($card['qrcodeurl']);
	        }
	    	$url = "BEGIN:VCARD
		            VERSION:3.0
		            FN:".$_GPC['realname']."
		            NICKNAME:".$_GPC['cardname']."
		            ORG:".$_GPC['company']."
	            	TITLE:".$_GPC['position']."
	            	TEL;TYPE=work:".$_GPC['tel']."
	            	TEL:".$_GPC['mobile']."
	            	EMAIL:".$_GPC['email']."
	            	END:VCARD";
    	    $mid = $this->createRandomNumber(8);
	        $file = 'ver_qrcode_' . $mid . '_' . $cid . '.png';
		    $qrcode_file = $paths . $file;
	        QRcode :: png($url, $qrcode_file, QR_ECLEVEL_L, 2);
		    $qrcode_img = $_W['siteroot'] . 'attachment/card_jpg/qrcode/' . $_W['uniacid'] . '/' . $file;
	        pdo_update('card',array('qrcodeurl' => $qrcode_file,'qrcode'=> $qrcode_img), array('id' => $cid));
            message($text,$this->createWebUrl('active',array('op'=>'display')));
        } else {
			message("操作失败");
		}
	}
    
}elseif ($op == 'output'){
    $list = pdo_fetchall("SELECT * FROM ".tablename('card')." WHERE uniacid=:uniacid",array(':uniacid'=>$_W['uniacid']));
    $arr = [];
    foreach ($list as $k =>$v){
		$arr[$k][] = $v['id'];
        $arr[$k][] = $v['realname'];
		$arr[$k][] = $v['cardname'];
		$arr[$k][] = $v['company'];
        $arr[$k][] = $v['position'];
		$arr[$k][] = $v['tel'];
		$arr[$k][] = $v['mobile'];
        $arr[$k][] = $v['address'];
		$arr[$k][] = $v['email'];
		$arr[$k][] = $v['wechat'];
	}
    exportexcelNew($arr, array('ID','姓名','昵称','公司','职务','电话','手机','地址','邮箱','微信'), 'card_'.time());
}
	
function exportexcelNew($data = array(), $title = array(), $filename = 'report',$head=null) {
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    Header( "Content-type:application/vnd.ms-excel ;charset=utf-8");//写编码
    header("Content-Disposition:attachment;filename=" . $filename . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    //导出xls 开始
    echo "<table width='100%' border='1' >"; //边框

    echo "<tr>";
    foreach ($title as $k => $v) {
        echo "<td  style='color:#8c654b;height:40px;' align='center'>  <font size=5>$v</font></td>";
    }
    echo "</tr>";
    foreach($data as $key=>$val){
        echo "<tr>";
        foreach($val as $k=>$v){
            echo "<td  style='color:#8c654b;' align='center'> ";
            $arr=explode("\r\n",$v);
            foreach($arr as $v1){
                echo "<font size=5>$v1</font><br/>";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    exit();
}

load()->func('tpl');
include $this->template('active');



?>
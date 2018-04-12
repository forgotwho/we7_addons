 <?php
/**
 * 首页
 */
defined('IN_IA') or exit('Access Denied');
global $_GPC, $_W;

$globalset=pdo_fetch("select * from ".tablename("nets_hjkcms_global")." where uniacid=:uniacid",array(":uniacid"=>$_W["uniacid"]));

if($_GPC['op']=='post'){
    $g['jdappid']=$_GPC['jdappid'];
    $g['jdappkey']=$_GPC['jdappkey'];
    $g['uniacid']=$_W['uniacid'];
    
    $i=0;
    if(empty($globalset)){
        $g['created_at']=time();
        $i= pdo_insert("nets_hjkcms_global",$g);
    }else{
        $g['updated_at']=time();
        $i= pdo_update("nets_hjkcms_global",$g);
    }
    message('操作成功！'," ","success");
}

include $this->template('web/index');

?>
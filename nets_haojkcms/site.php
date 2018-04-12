<?php
/**
 * 模块微站定义
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
class Nets_haojkcmsModuleSite extends WeModuleSite {

	function __construct()
    {
        global $_W,$_GPC;
		
	
        /*
		* 是否测试环境
        */
		
		$debug=true;
		if($debug){
			$_W["openid"]="oW2uxv1o1ilYRrzZgaSux5FXDVkQ";
			$_W["member"]['uid']=2002;
		}else{
			
		}
		$hjkcms_global=pdo_fetch("select * from ".tablename("nets_hjkcms_global")." where uniacid=:uniacid",array(":uniacid"=>$_W["uniacid"]));
		$_W["hjkcms_global"]=$hjkcms_global;
		
		
    }
}
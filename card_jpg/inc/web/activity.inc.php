<?php
global $_W,$_GPC;

$op=$_GPC['op']?$_GPC['op']:'display';

if ($op=='display'){
	$item['title'] = '活动入口';
	$item['url_show'] = murl('entry', array('do' => 'active','m' => 'card_jpg'), true, true);
    $url = str_replace('&','%26',$item['url_show']);
	$item['url'] = "http://qr.topscan.com/api.php?text=".$url;
	$item['poster'] = $_W['siteroot'] . "addons/card_jpg/template/image/EnterprisesCard.png";
	$filesize = @getimagesize($item['poster']);
	if(!$filesize){
		$pimg = $_W['siteroot'] . "addons/card_jpg/template/image/posterQrcode.png";
		$pg = imagecreatefrompng($pimg);
		$posterdata = @getimagesize($pimg);
		$pgm = imagecreatetruecolor($posterdata[0], $posterdata[1]);
	    imagecopy($pgm,$pg,0,0,0,0,$posterdata[0],$posterdata[1]);
		imagedestroy($pg);
		
		$qrcodedata = @getimagesize($item['url']);
		$qr =  imagecreatefrompng($item['url']);
		$qrm = imagecreatetruecolor(600, 600);
		imagecopyresampled($qrm,$qr,0,0,0,0,600,600,$qrcodedata[0],$qrcodedata[1]);
		imagedestroy($qr);
		imagecopymerge($pgm, $qrm, 321, 600, 0, 0, imagesx($qrm),imagesy($qrm), 100);
		$imgyum = IA_ROOT . "/addons/card_jpg/template/image/EnterprisesCard.png";
        imagepng($pgm,$imgyum);
	}
}

include $this->template('activity');

?>
<?php
    global $_W,$_GPC;
	$uid = $_GPC['id'];
	if(!$uid){
		message('参数为空',$this->createWebUrl('active',array('op'=>'display')));
	}
	$card = pdo_fetch("SELECT * FROM " . tablename('card') . " where uid = :uid and uniacid = :uniacid", array(':uid' => $uid,':uniacid' => $_W['uniacid']));
	if(!$card){
		message('数据为空',$this->createWebUrl('active',array('op'=>'display')));
	}
	
	$attachment = IA_ROOT . "/attachment/card_jpg/";
	if(!file_exists($attachment)){
	    mkdir($attachment);
	}
	$logo_file = IA_ROOT . "/attachment/card_jpg/logo/";
	if(!file_exists($logo_file)){
	    mkdir($logo_file);
	}
	$headimg_file = IA_ROOT . "/attachment/card_jpg/headimg/";
	if(!file_exists($headimg_file)){
	    mkdir($headimg_file);
	}
	$card_file = IA_ROOT . "/attachment/card_jpg/card/";
	if(!file_exists($card_file)){
	    mkdir($card_file);
	}
	
	if($card['headimg']){
		$xt = $card['headimg']; //头像
	} else {
		$hs   = 180;
	    $ws   = 180;
		if($card['image']){
			$headimg = tomedia($card['image']);
			$cardheadimg = getimagesize($headimg);
		} else {
			$carduser = pdo_fetch("SELECT * FROM " . tablename('card_user') . " where id = :id ", array(':id' => $uid));
			if($carduser['headimgurl']){
				$headimg = $carduser['headimgurl'];
		        $cardheadimg = getimagesize($headimg);
			} else {
				$headimg = $_W['siteroot'] . "addons/card_jpg/template/image/card.png";
		        $cardheadimg = getimagesize($headimg);
			}
		}
		$cu = imagecreatetruecolor($ws, $hs);
		switch ($cardheadimg['mime']){
	    	case 'image/jpeg':
		        $cuimg = imagecreatefromjpeg($headimg);
		    	break;
		    case 'image/gif':
	     	    $cuimg = imagecreatefromgif($headimg);
	     		break;
	    	case 'image/wbmp':
	    	    $cuimg = imagecreatefromwbmp($headimg);
	    		break;
	    	default:
	    	    $cuimg = imagecreatefrompng($headimg);
	    		break;
	    }
		imagecopyresampled($cu,$cuimg,0,0,0,0,$ws,$hs,$cardheadimg[0],$cardheadimg[1]);
		imagedestroy($cuimg);
		$path = IA_ROOT . "/attachment/card_jpg/headimg/" . $_W['uniacid'] . "/";
    	if(!file_exists($path)){
	        mkdir($path);
	    }
		$headimgurl = $path.$card['id'].".png";
        imagepng($cu,$headimgurl);
		$xt = $_W['siteroot'] . 'attachment/card_jpg/headimg/' . $_W['uniacid'] . '/' . $card['id'].".png";
		pdo_update('card',array('headimgurl' => $headimgurl,'headimg'=> $xt), array('id' => $card['id']));
		imagedestroy($cu);
	}
	$colortype = $card['colortype']?$card['colortype']:'white';
	
	$xg = imagecreatefrompng($xt);
	$wh  = getimagesize($xt); //载入头像路径
	$w = $wh[0];
	$h = $wh[1];
	$xgm = imagecreatetruecolor($w, $h);
	imagecopy($xgm,$xg,0,0,0,0,$w,$h);
	imagedestroy($xg);
    imagealphablending($xgm,false);
	
	if($card['baseimage']){
		if($card['logo']){
			$logo = $card['logo'];
		} else {
			$baseimage = tomedia($card['baseimage']);
			$baseimagetype = getimagesize($baseimage);
			if($card['model'] == '4'){
				$baseimg_w = 400;
				$baseimg_h = 720;
			} else {
				$baseimg_w = 720;
				$baseimg_h = 440;
			}
			$bm = imagecreatetruecolor($baseimg_w, $baseimg_h);
		    switch ($baseimagetype['mime']){
	    	    case 'image/jpeg':
		            $bmimg = imagecreatefromjpeg($baseimage);
		    	    break;
		        case 'image/gif':
	     	        $bmimg = imagecreatefromgif($baseimage);
	     		    break;
	    	    case 'image/wbmp':
	    	        $bmimg = imagecreatefromwbmp($baseimage);
	    		    break;
	    	    default:
	    	        $bmimg = imagecreatefrompng($baseimage);
	    		    break;
	        }
			imagecopyresampled($bm,$bmimg,0,0,0,0,$baseimg_w,$baseimg_h,$baseimagetype[0],$baseimagetype[1]);
			imagedestroy($bmimg);
		    $logopath = IA_ROOT . "/attachment/card_jpg/logo/" . $_W['uniacid'] . "/";
    	    if(!file_exists($logopath)){
	            mkdir($logopath);
	        }
			$logotime = time();
		    $logourl = $logopath.'cardlogo_'.$card['id'].'_'.$logotime.".png"; //生成图片的路径
            imagepng($bm,$logourl);
		    $logo = $_W['siteroot'] . 'attachment/card_jpg/logo/' . $_W['uniacid'] . '/' .'cardlogo_'.$card['id'].'_'.$logotime.".png";
		    pdo_update('card',array('logourl' => $logourl,'logo'=> $logo), array('id' => $card['id']));
		    imagedestroy($bm);
		}
	} else {
		if($card['model'] == '4'){
			$logo = $_W['siteroot'] . 'addons/card_jpg/data/color/'.$colortype.'_400.png'; //背景图
		} else {
			$logo = $_W['siteroot'] . 'addons/card_jpg/data/color/'.$colortype.'_720.png'; //背景图
		}
	}
    $bg = imagecreatefrompng($logo); //载入图片路径
    $img_info = getimagesize($logo); //获取图片大小
    $img_w = $img_info[0];
    $img_h = $img_info[1];
    $im = imagecreatetruecolor($img_w, $img_h);
	imagecopy($im,$bg,0,0,0,0,$img_w,$img_h);
    imagedestroy($bg);
	
	$font_color = $card['fontcolor']?$card['fontcolor']:'black';
	switch ($font_color){
	    case 'white':
			$fontcolor = imagecolorallocate($im, 255, 255, 255);
		    break;
		case 'black':
			$fontcolor = imagecolorallocate($im, 0, 0, 0);
		    break;
	    case 'blue':
			$fontcolor = imagecolorallocate($im, 135,206,235);
		    break;
		case 'darkred':
			$fontcolor = imagecolorallocate($im, 139, 0, 0);
		    break;
		case 'silver':
		    $fontcolor = imagecolorallocate($im, 192, 192, 192);
		    break;
		case 'green':
		    $fontcolor = imagecolorallocate($im, 0, 128, 0);
		    break;
		case 'purple':
	    	$fontcolor = imagecolorallocate($im, 128, 0, 128);
		    break;
		case 'gold':
	    	$fontcolor = imagecolorallocate($im, 255, 215, 0);
		    break;
		case 'cyan':
	    	$fontcolor = imagecolorallocate($im, 0, 255, 255);
		    break;
	    default:
			$fontcolor = imagecolorallocate($im, 0, 0, 0);
		    break;
	}
	$qr = imagecreatefrompng($card['qrcode']);
	$qrcode_info = getimagesize($card['qrcode']); //获取图片大小
    $qrcode_w = $qrcode_info[0];
    $qrcode_h = $qrcode_info[1];
	$qrim = imagecreatetruecolor(150, 150);
	imagecopyresampled($qrim,$qr,0,0,0,0,150,150,$qrcode_w,$qrcode_h);
    imagedestroy($qr);
	
	$fonttype = $card['font']?$card['font']:'sjt';
	$font = '../addons/card_jpg/data/font/'.$fonttype.'.ttf';
	$model = $card['model']?$card['model']:'1';
	switch ($model){
	    case '1':
		    imagecopymerge($im, $xgm, 42, 30, 0, 0, imagesx($xgm),imagesy($xgm), 100);
	        imagecopymerge($im, $qrim, 59, 235, 0, 0, imagesx($qrim),imagesy($qrim), 100);
			imagettftext($im, 14, 0, 58, 407, $fontcolor, $font, '识别二维码存名片');
		    
			if($card['realname']){
				imagettftext($im, 21, 0, 290, 90, $fontcolor, $font, $card['realname']);
				imagettftext($im, 12, 0, 455, 90, $fontcolor, $font, $card['position']);
			}
			
			if($card['company']){
            	imagettftext($im, 22, 0, 290, 208, $fontcolor, $font, $card['company']);
			}
			
			$i = 210;
			if($card['tel'] && $card['mobile']){
				$i = $i + 38;
				$phone = $_W['siteroot'] . 'addons/card_jpg/data/icon/phone.png';
	            $phoneimg = imagecreatefrompng($phone);
	            imagecopymerge($im, $phoneimg, 290, $i, 0, 0, 27, 27, 100);
	            imagedestroy($phoneimg);
				imagettftext($im, 12, 0, 328, $i+4, $fontcolor, $font, $card['tel']);
        	    imagettftext($im, 12, 0, 328, $i+30, $fontcolor, $font, $card['mobile']);
			} else {
				if($card['tel']){
					$i = $i + 38;
					$phone = $_W['siteroot'] . 'addons/card_jpg/data/icon/phone.png';
	                $phoneimg = imagecreatefrompng($phone);
	                imagecopymerge($im, $phoneimg, 290, $i, 0, 0, 27, 27, 100);
	                imagedestroy($phoneimg);
					imagettftext($im, 12, 0, 328, $i+20, $fontcolor, $font, $card['tel']);
				} 
				if($card['mobile']){
					$i = $i + 38;
					$phone = $_W['siteroot'] . 'addons/card_jpg/data/icon/phone.png';
	                $phoneimg = imagecreatefrompng($phone);
	                imagecopymerge($im, $phoneimg, 290, $i, 0, 0, 27, 27, 100);
	                imagedestroy($phoneimg);
					imagettftext($im, 12, 0, 328, $i+20, $fontcolor, $font, $card['mobile']);
				}
			}
	        
			if($card['wechat']){
				$i = $i + 38;
				$wechat = $_W['siteroot'] . 'addons/card_jpg/data/icon/wechat.png';
             	$wechatimg = imagecreatefrompng($wechat);
            	imagecopymerge($im, $wechatimg, 290, $i, 0, 0, 27, 27, 100);
        	    imagedestroy($wechatimg);
            	imagettftext($im, 12, 0, 328, $i+20, $fontcolor, $font, $card['wechat']);
			}
			
        	if($card['email']){
				$i = $i + 38;
        	    $mail = $_W['siteroot'] . 'addons/card_jpg/data/icon/mail.png';
            	$mailimg = imagecreatefrompng($mail);
            	imagecopymerge($im, $mailimg, 290, $i, 0, 0, 27, 27, 100);
            	imagedestroy($mailimg);
            	imagettftext($im, 12, 0, 328, $i+20, $fontcolor, $font, $card['email']);
			}
			
			if($card['address']){
				$i = $i + 38;
        	    $add = $_W['siteroot'] . 'addons/card_jpg/data/icon/add.png';
        	    $addimg = imagecreatefrompng($add);
             	imagecopymerge($im, $addimg, 290, $i, 0, 0, 27, 27, 100);
            	imagedestroy($addimg);
		    	$len = strlen($card['address']);
		    	if($len < 55){
				    imagettftext($im, 12, 0, 328, $i+20, $fontcolor, $font, $card['address']);
		    	} else {
		    		$first_line = substr($card['address'] , 0, 54);
	    			imagettftext($im, 12, 0, 328, $i+20, $fontcolor, $font, $first_line);
		    		$second_line =  str_replace($first_line,"",$card['address']);
		    		imagettftext($im, 12, 0, 328, $i+45, $fontcolor, $font, $second_line);
		    	}
			}
		    break;
		case '2':
		    imagecopymerge($im, $xgm, 30, 37, 0, 0, imagesx($xgm),imagesy($xgm), 100);
	        imagecopymerge($im, $qrim, 522, 40, 0, 0, imagesx($qrim),imagesy($qrim), 100);
			imagettftext($im, 13, 0, 528, 212, $fontcolor, $font, '识别二维码存名片');
			
			if($card['realname']){
				imagettftext($im, 24, 0, 240, 85, $fontcolor, $font, $card['realname']);
			    imagettftext($im, 14, 0, 345, 110, $fontcolor, $font, $card['position']);
			}
			
			$i = 265;
			if($card['company']){
				$i = $i + 35;
			    imagettftext($im, 24, 0, 35, $i, $fontcolor, $font, $card['company']);
			}
			
			if($card['tel'] && $card['mobile']){
				$i = $i + 35;
				$tel = 'telephone : '.$card['tel'];
				$mobile = 'mobile : '.$card['mobile'];
		    	imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $mobile);
            	imagettftext($im, 14, 0, 370, $i, $fontcolor, $font, $tel);
			} else {
				if($card['tel']){
					$i = $i + 35;
					$tel = 'telephone : '.$card['tel'];
			        imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $tel);
				}
				if($card['mobile']){
					$i = $i + 35;
					$mobile = 'mobile : '.$card['mobile'];
        	        imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $mobile);
				}
			}
			
			if($card['email'] && $card['wechat']){
				$i = $i + 35;
			    $email = 'email : '.$card['email'];
            	imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $email);
				$wechat = 'wechat : '.$card['wechat'];
			    imagettftext($im, 14, 0, 370, $i, $fontcolor, $font, $wechat);
			} else {
				if($card['email']){
				    $i = $i + 35;
			        $email = 'email : '.$card['email'];
            	    imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $email);
		    	}
			    if($card['wechat']){
				    $i = $i + 35;
				    $wechat = 'wechat : '.$card['wechat'];
			        imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $wechat);
			    }
			}
			
			if($card['address']){
				$i = $i + 35;
			    imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, 'address : '.$card['address']);
			}
		    break;
		case '3':
		    imagecopymerge($im, $xgm, 30, 37, 0, 0, imagesx($xgm),imagesy($xgm), 100);
	        imagecopymerge($im, $qrim, 522, 40, 0, 0, imagesx($qrim),imagesy($qrim), 100);
			imagettftext($im, 13, 0, 528, 212, $fontcolor, $font, '识别二维码存名片');
			
			if($card['realname']){
				imagettftext($im, 24, 0, 240, 85, $fontcolor, $font, $card['realname']);
			    imagettftext($im, 14, 0, 345, 110, $fontcolor, $font, $card['position']);
			}
			
			$i = 265;
			if($card['company']){
				$i = $i + 35;
			    imagettftext($im, 24, 0, 35, $i, $fontcolor, $font, $card['company']);
			}
			
			if($card['tel'] && $card['mobile']){
				$i = $i + 35;
				$mobile = '手机号码 : '.$card['mobile'];
				$tel = '公司电话 : '.$card['tel'];
		    	imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $mobile);
            	imagettftext($im, 14, 0, 370, $i, $fontcolor, $font, $tel);
			} else {
				if($card['tel']){
					$i = $i + 35;
					$tel = '公司电话 : '.$card['tel'];
			        imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $tel);
				}
				if($card['mobile']){
					$i = $i + 35;
					$mobile = '手机号码 : '.$card['mobile'];
        	        imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $mobile);
				}
			}
			
			if($card['email'] && $card['wechat']){
				$i = $i + 35;
			    $email = '电子邮件 : '.$card['email'];
            	imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $email);
				$wechat = '微信 : '.$card['wechat'];
			    imagettftext($im, 14, 0, 370, $i, $fontcolor, $font, $wechat);
			} else {
				if($card['email']){
				    $i = $i + 35;
			        $email = '电子邮件 : '.$card['email'];
            	    imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $email);
		    	}
			    if($card['wechat']){
				    $i = $i + 35;
				    $wechat = '微信 : '.$card['wechat'];
			        imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, $wechat);
			    }
			}
			
			if($card['address']){
				$i = $i + 35;
			    imagettftext($im, 14, 0, 35, $i, $fontcolor, $font, '地址 : '.$card['address']);
			}
		    break;
		case '4':
		    imagecopymerge($im, $xgm, 110, 55, 0, 0, imagesx($xgm),imagesy($xgm), 100);
	        imagecopymerge($im, $qrim, 230, 264, 0, 0, imagesx($qrim),imagesy($qrim), 100);
			imagettftext($im, 10, 0, 254, 430, $fontcolor, $font, '识别二维码存名片');
			
			if($card['realname']){
				$i = 480;
			    imagettftext($im, 20, 0, 25, 458, $fontcolor, $font, $card['realname']);
			    imagettftext($im, 10, 0, 120, 478, $fontcolor, $font, $card['position']);
			}
			
			$i = 480;
			if($card['company']){
				$i = $i + 30;
				imagettftext($im, 18, 0, 25, $i, $fontcolor, $font, $card['company']);
			}
			
			if($card['tel']){
				$i = $i + 30;
				$tel = '公司电话 : '.$card['tel'];
				imagettftext($im, 12, 0, 25, $i, $fontcolor, $font, $tel);
			}
			
			if($card['mobile']){
				$i = $i + 30;
				$mobile = '手机号码 : '.$card['mobile'];
				imagettftext($im, 12, 0, 25, $i, $fontcolor, $font, $mobile);
			}
			
			if($card['wechat']){
				$i = $i + 30;
				$wechat = '微信号码 : '.$card['wechat'];
				imagettftext($im, 12, 0, 25, $i, $fontcolor, $font, $wechat);
			}
			
			if($card['email']){
				$i = $i + 30;
				$email = '电子邮件 : '.$card['email'];
				imagettftext($im, 12, 0, 25, $i, $fontcolor, $font, $email);
			}
			
			if($card['address']){
				$i = $i + 30;
			    imagettftext($im, 12, 0, 25, $i, $fontcolor, $font, $card['address']);
			}
		    break;
	    default:
		    break;
	}
	
	$imgyumurl = IA_ROOT . '/attachment/card_jpg/card/'.$_W['uniacid'] .'/';
	if(!file_exists($imgyumurl)){
	    mkdir($imgyumurl);
	}
	if($card['picurl']){
		unlink($card['picurl']);
	}
	$time = time();
    $imgyum = $imgyumurl.'cardpic_'.$card['id'].'_'.$time.".png";//生成图片的路径
    imagepng($im,$imgyum);
	$route = $_W['siteroot'] . 'attachment/card_jpg/card/' . $_W['uniacid'] . '/'.'cardpic_'.$card['id'].'_'.$time.".png";
	pdo_update('card',array('picurl' => $imgyum,'pic'=> $route), array('id' => $card['id']));
	imagedestroy($im);
	message('名片生成成功',$this->createWebUrl('active',array('op'=>'display')));
	
?>
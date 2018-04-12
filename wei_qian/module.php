<?php
/**
 * 签到模块定义
 *
 * @author zhouchong
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Wei_qianModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
	}
	function _tpl_form_field_dates($name, $value = '', $withtime = false) {
	$s = '';
	$withtime = empty($withtime) ? false : true;
	if (!empty($value)) {
		$value = strexists($value, '-') ? strtotime($value) : $value;
	} else {
		$value = TIMESTAMP;
	}
	//$value = ($withtime ? date('H:i', $value) : date('H:i', $value));
	$s .= '<input type="text" name="' . $name . '"  value="'.$value.'" placeholder="请选择日期时间" readonly="readonly" class="datetimepicker form-control" style="padding-left:12px;" />';
	$s .= '
		<script type="text/javascript">
			require(["datetimepicker"], function(){
					var option = {
						lang : "zh",
						datepicker:false,
	format:"H:i",
	step:5
					};
				$(".datetimepicker[name = \'' . $name . '\']").datetimepicker(option);
			});
		</script>';
	return $s;
}
	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		$sql = 'SELECT * FROM ' . tablename('wei_qian_peizhi') . ' WHERE uniacid = :uniacid';
	    $settings = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
	    $this->settings = $settings;
		$str = "20180113";
		$bbhao = MD5($str);
		
		/* $m = cloud_m_info('wei_qian');
		print_r($m); */
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$this->saveSettings($dat);
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}
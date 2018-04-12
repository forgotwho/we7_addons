<?php
defined('IN_IA') or exit('Access Denied');

class We7_enterpriseModuleWebapp extends WeModuleWebapp {

	public function doPageFengmian() {
		global $_W;
		$lists = pdo_getall('we7_enterprise_news', array('uniacid' => $_W['uniacid']), '', 'id desc', array(1, 20));
		$setting = $this->getWebSetting();
		include $this->template('webappindex');
	}

	public function doPageDetail() {
		global $_GPC;
		$id = $_GPC['id'];
		$list = pdo_get('we7_enterprise_news', array('id' => $id));
		$setting = $this->getWebSetting();
		include $this->template('news-detail');
	}

	public function doPageList() {
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$lists = pdo_getslice('we7_enterprise_news', array('uniacid' => $_W['uniacid']), array($pindex, $psize), $total, array(), '', 'id desc');
		$pager = pagination($total, $pindex, $psize);
		$setting = $this->getWebSetting();
		include $this->template('news-list');
	}

	public function doPageAbout() {
		global $_GPC, $_W;
		$setting = $this->getWebSetting();
		include $this->template('about');
	}

	public function getWebSetting() {
		global $_W, $_GPC;
		$setting = pdo_get('we7_enterprise_setting', array('key' => 'setting', 'uniacid' => $_W['uniacid']));
		if (!empty($setting['value'])) {
			$setting = iunserializer($setting['value']);
		}
		return $setting;
	}
}
<?php
defined('IN_IA') or exit('Access Denied');

class We7_enterpriseModuleSite extends WeModuleSite {

	public function doWebNews() {
		global $_W, $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$condition = array();
		$condition['uniacid'] = $_W['uniacid'];
		$keyword = safe_gpc_string($_GPC['title'], '');
		if (!empty($keyword)) {
			$condition['title LIKE'] = "%{$keyword}%";
		}
		$lists = pdo_getslice('we7_enterprise_news', $condition, array($pindex, $psize), $total,'', 'id', 'id desc');
		$pager = pagination($total, $pindex, $psize);
		include $this->template('display');
	}

	public function doWebNews_add() {
		global $_W, $_GPC;

		$id = safe_gpc_int($_GPC['id']);
		$list = pdo_get('we7_enterprise_news', array('id' => $id));

		if (checksubmit('submit')) {
			if (empty($_GPC['title']) || empty($_GPC['content'])) {
				itoast('标题或内容不能为空', referer());
			}

			$data = array(
				'title' => safe_gpc_string($_GPC['title']),
				'content' => htmlspecialchars_decode($_GPC['content']),
				'create_time' => TIMESTAMP,
				'uniacid' => $_W['uniacid']
			);

			if (!empty($id)) {
				pdo_update('we7_enterprise_news', $data, array('id' => $id));
				message('修改成功', $this->createWebUrl('news'));
			} else {
				pdo_insert('we7_enterprise_news', $data);
				message('添加成功', $this->createWebUrl('news'));
			}
		}
		include $this->template('post');
	}

	public function doWebNews_delete() {
		global $_W, $_GPC;
		$id = safe_gpc_int($_GPC['id']);
		if (empty($id)) {
			itoast('非法操作', $this->createWebUrl('news'));
		}
		pdo_delete('we7_enterprise_news', array('id' => $id));
		itoast('删除成功', $this->createWebUrl('news'));
	}

	public function doWebSet() {
		global $_W, $_GPC;
		$setting = pdo_get('we7_enterprise_setting', array('key' => 'setting', 'uniacid' => $_W['uniacid']));
		if (!empty($setting['value'])) {
			$setting = iunserializer($setting['value']);
		}

		if ($_W['ispost']) {
			$uni_setting['qq'] = safe_gpc_string($_GPC['qq']);
			$uni_setting['name'] = safe_gpc_string($_GPC['name']);
			$uni_setting['address'] = safe_gpc_string($_GPC['address']);
			$uni_setting['slides'] = safe_gpc_string($_GPC['slides']);
			$uni_setting['about'] = safe_gpc_string($_GPC['about']);
			$uni_setting['copyright'] = safe_gpc_string($_GPC['copyright']);
			$uni_setting['flogo'] = safe_gpc_string($_GPC['flogo']);
			$uni_setting = iserializer($uni_setting);
			if (!empty($setting)) {
				pdo_update('we7_enterprise_setting', array('value' => $uni_setting), array('key' => 'setting', 'uniacid' => $_W['uniacid']));
			} else {
				pdo_insert('we7_enterprise_setting', array('key' => 'setting', 'value' => $uni_setting, 'uniacid' => $_W['uniacid']));
			}
			itoast('设置成功！', $this->createWebUrl('set'), 'success');
		}
		include $this->template('web-setting');
	}
}
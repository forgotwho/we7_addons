<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once IA_ROOT . '/addons/card_jpg/defines.php';

class Card_jpgModule extends WeModule {
    /**
     * 模块入口页面重置
     */
//    public function welcomeDisplay() {
//        exit("welcome");
//    }

    public function fieldsFormDisplay($rid = 0) {
        //要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
        global $_W, $_GPC;
        /**
         * 此处分为两种情况，新增规则或是修改规则。
         * 如果rid不为0，则需要查询出此规则对应的回复数据。
         */
        if (!empty($rid)) {
            $item = pdo_fetch("SELECT * FROM ".tablename('test_config')." WHERE rid = :rid", array(':rid' => $rid));
        }
        // 调用模板页面
        include $this->template('rule');
    }

    public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        global $_GPC;
        //此处服务端验证表单数据的完整性，直接返回错误信息。
        if (empty($_GPC['content'])) {
            return '请填写回复内容';
        }
        return '';
    }

    public function fieldsFormSubmit($rid) {
        //规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
        global $_W, $_GPC;
        /*
         * 此处各种验证通过后，需要进行入库操作。
         * 入库时需要注意，此处数据可能为更新操作也可能为新增数据。
         */
        $data = array(
            'rid' => $rid,
            'content' => $_GPC['content'],
        );
        $id = pdo_fetchcolumn("SELECT id FROM ".tablename('we7_demo_reply')." WHERE rid = :rid", array(':rid' => $rid));
        if (empty($id)) {
            pdo_insert('we7_demo_reply', $data);
        } else {
            pdo_update('we7_demo_reply', $data, array('id' => $id));
        }
    }

    public function ruleDeleted($rid) {
        //删除规则时调用，这里 $rid 为对应的规则编号
        /*
         * 此处可能需要一些权限及数据方面的判断
         * 除了表数据可能还需要删除一些附带的图片等资源
         */
        pdo_delete('we7_demo_reply', array('rid' => $rid));
    }

    public function settingsDisplay($settings) {
        global $_W, $_GPC;
        $tplid = pdo_fetch("SELECT * FROM  " . tablename('vote_setting') . " where weid =:weid", array(':weid' => $_W['uniacid']));

        //点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
        //在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
        if(checksubmit('submit')) {
            $data=[
                'qcode'=>$_GPC['qcode'],
                'is_follow'=>$_GPC['is_follow'],
                'weid'=>$_W['uniacid'],
            ];
            if (empty($tplid)){
                $res=pdo_insert('vote_setting',$data);
                if ($res){
                    message('添加成功.', '', 'success');
                }else{
                    message('添加失败.', '', 'error');
                }
            }else{
                $res=pdo_update('vote_setting',array('qcode'=>$_GPC['qcode'],'is_follow'=>$_GPC['is_follow']),array('weid'=>$_W['uniacid']));
                if ($res){
                    message('更新成功.', '', 'success');
                }else{
                    message('更新失败.', '', 'error');
                }
            }

        }
        //这里来展示设置项表单
        include $this->template('setting');
    }

}
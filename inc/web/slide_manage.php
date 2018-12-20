<?php
global $_W,$_GPC;

checklogin();

	$title = '快递设置';
  	 
	$express_slide='yiheng_express_slide';




$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$list = pdo_fetchall("SELECT * FROM " . tablename($express_slide) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY slide_displayorder DESC");
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (checksubmit('submit')) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'slide_name' => $_GPC['advname'],
			'slide_link' => $_GPC['link'],
			'slide_status' => intval($_GPC['enabled']),
			'slide_displayorder' => intval($_GPC['displayorder']),
			'slide_thumb'=>$_GPC['thumb']
		);
		if (!empty($id)) {
			pdo_update($express_slide, $data, array('id' => $id));
		} else {
			pdo_insert($express_slide, $data);
			$id = pdo_insertid();
		}
		message('更新幻灯片成功！', $this->createWebUrl('slide_manage', array('op' => 'display')), 'success');
	}
	$adv = pdo_fetch("select * from " . tablename($express_slide) . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $_W['uniacid']));
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$adv = pdo_fetch("SELECT id FROM " . tablename($express_slide) . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
	if (empty($adv)) {
		message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('slide_manage', array('op' => 'display')), 'error');
	}
	pdo_delete($express_slide, array('id' => $id));
	message('幻灯片删除成功！', $this->createWebUrl('slide_manage', array('op' => 'display')), 'success');
} else {
	message('请求方式不存在');
}
include $this->template('web/slide_manage');
?>
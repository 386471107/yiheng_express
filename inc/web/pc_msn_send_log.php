


<?php 

	global $_GPC, $_W;
	

	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';

	$wopt = new yh_web_option();

	$express_notice_log='yiheng_express_notice_log';


	$nav = $wopt->page_info();


	$def_shop_id = $wopt->Get_web_def_shop();

	$pindex = max(1, intval($_GPC['page']));

	$list = $wopt->Get_web_msn_sendlog($def_shop_id,$pindex,$psize=100);

	 
 
	foreach ($list['lst'] as $key => $value) {
		//$list['lst'][$key]['realname'] = $wopt->Get_realname_by_openid($value['send_openid']);
		$list['lst'][$key]['shop_name'] = $wopt->Get_shop_name($value['send_shop_id']);
	}
	

	

	
	include $this->template('pc/pc_msn_send_log');  
	
	
	
?>
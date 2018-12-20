<?php 


	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();
	$nav = $wopt->page_info();
	
	$def_shop_id = $wopt->Get_web_def_shop();

	$pindex = max(1, intval($_GPC['page']));
	$level=(int)($_GPC['level']);
	$list = $wopt->Get_web_member_list($def_shop_id,$level,$pindex,$psize=50);
	$mlist =$list['mlist'];


	foreach ($mlist as $key => $value) {
		// $mlist[$key]['shop_name'] =$wopt-> Get_shop_name($value['lv_shop_id']);
		$mlist[$key]['sms_surplus'] =$wopt-> Get_sms_surplus($def_shop_id,$value['bind_m_openid']);
		
	}


	
	 
	include $this->template('pc/pc_member_staff_list');  
	 
	
?>

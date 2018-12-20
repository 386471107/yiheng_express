<?php 

	global $_GPC, $_W;
	

	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';

	$wopt = new yh_web_option();

	$express_notice_log='yiheng_express_notice_log';


	$nav = $wopt->page_info();


	$def_shop_id = $wopt->Get_web_def_shop();
	

	$pindex = max(1, intval($_GPC['page']));

	$list = $wopt->Get_web_weixing_sendlog($def_shop_id,$pindex,$psize=100);

	$shopname=$wopt->Get_shop_name($def_shop_id);
	
	$sql = "SELECT * FROM `ims_yiheng_express_member_bind` WHERE `bind_shop_id` =$def_shop_id AND `bind_m_level` <>0";
	
	$manage_list = pdo_fetchall($sql);
	
	foreach($manage_list as $k=> $ml)
	{
		$manage_list[$k]['realname']=$wopt->Get_realname_by_openid($ml['bind_m_openid']);
	}
	
	
	
	
	foreach ($list['lst'] as $key => $value) {
		//$list['lst'][$key]['realname'] = $wopt->Get_realname_by_openid($value['send_openid']);
		//$list['lst'][$key]['nickname'] = $wopt->Get_realname_by_openid($value['send_to_openid']);		
		$list['lst'][$key]['shop_name'] =$shopname;
	}
	

	
	include $this->template('pc/pc_weixin_send_log');  
	
	
	
?>
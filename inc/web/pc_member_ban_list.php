<?php 

	global $_GPC, $_W;

	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();
	$nav = $wopt->page_info();

	$def_shop_id = $wopt->Get_web_def_shop();

	$pindex = max(1, intval($_GPC['page']));
	$level=(int)($_GPC['level']);
	$list = $wopt->Get_web_ban_member_list($def_shop_id,1,$pindex,$psize=50);
	
	$mlist =$list['mlist'];

	include $this->template('pc/pc_member_ban_list');  
	
	
	
?>
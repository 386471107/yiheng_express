<?php 

	global $_GPC, $_W;


	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';

	// $sql ="select * from ims_yiheng_express_tel_list";
	
	// $a = pdo_fetchall($sql);
	
	
	
	// foreach($a as $v)
	// {
		// $b= substr($v['m_tel'],-5);
		
		// pdo_update('yiheng_express_tel_list',array('tl_tel_end_no'=>$b),array('id'=>$v['id']));
	// }

	
	
	$wopt = new yh_web_option();
	$nav = $wopt->page_info();
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$list=$wopt-> Get_web_shop_list_by_weid($pindex,$psize);
	$shop_list =$list['lst'];
	$pager=$list['pager'];
	include $this->template('pc/pc_shop_list');  
	
	
	
?>
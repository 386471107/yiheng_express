<?php 

	global $_GPC, $_W;



	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();
	$nav = $wopt->page_info();

	

	$api_list = $wopt->Get_web_api_list_by_weid();


	

	include $this->template('pc/pc_api');  

?>
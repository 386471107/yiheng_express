<?php 
	global $_GPC, $_W;

	include_once MODULE_ROOT.'/inc/mobile/common.php';
	$title = '快递后台管理';
	
	if ($myinfo['m_level']==4)
	{
		$url=$this->createMobileUrl('employee');
		header("Location: $url");exit();
	
	}
	
	//走权限管理
	$page="home";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理
	
	$shop = $member->Get_shop_name($myinfo['m_defaut_area']);
	$opt->Update_to_retention($openid,$hours=48); 
	include $this->template('home');  
	exit();

?>
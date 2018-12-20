<?php 

	global $_GPC, $_W;

	// include_once MODULE_ROOT.'/inc/func/yh_kd_curl.php';
	// $kdapiA = new yh_kd_curl();
	// $LogisticCode= '249806902689';
	// $kdapiA ->Get_express_company($LogisticCode);
	// exit();
	include_once MODULE_ROOT.'/inc/func/yh_safe.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();
	$safe = new yh_safe_check();
	$nav = $wopt->page_info();
	$def_shop_id = $wopt->Get_web_def_shop();
	$shop_info= $wopt->Get_web_shop_info($def_shop_id);

	$manage_msn_total= $wopt->Get_manage_msn_total($def_shop_id);


	$year=date("Y",time());
	$month=date("m",time());
	$day=date("d",time());
	$hour=date("H",time());
	$s_in = $wopt->Get_statistics_by_day_total($year,$month,$day,'s_in',$def_shop_id);
	$s_out= $wopt->Get_statistics_by_day_total($year,$month,$day,'s_out',$def_shop_id);
	$s_wx_notice= $wopt->Get_statistics_by_day_total($year,$month,$day,'s_wx_notice',$def_shop_id);
	$s_message= $wopt->Get_statistics_by_day_total($year,$month,$day,'s_message',$def_shop_id);
	$s_message_price = $shop_info['area_msn_price']*$s_message;
	$hour_in_data=$hour_out_data=$s_date=array();
	for($i = 0;$i <24;$i ++)
	{
		$hour_in = $wopt->Get_recode_in_by_hour_day($def_shop_id,$year,$month,$day,$i);
		$hour_out = $wopt->Get_recode_out_by_hour_day($def_shop_id,$year,$month,$day,$i);
		array_push($hour_in_data,$hour_in);
		array_push($hour_out_data,$hour_out);
		array_push($s_date,$i);
	}
		
		$sum_in=array_sum($hour_in_data);
		$sum_out=array_sum($hour_out_data);

	$json_hour_in_data= json_encode($hour_in_data);
	$json_hour_out_data= json_encode($hour_out_data);

	$json_s_date= json_encode($s_date);

	$wx_send_list_temp = $wopt->Get_web_weixing_sendlog($def_shop_id,1,10);
	
	foreach ($wx_send_list_temp['lst'] as $key => $value) {
		$wx_send_list_temp['lst'][$key]['nickname'] = $wopt->Get_realname_by_openid($value['send_to_openid']);
	}

	$wx_send_list=$wx_send_list_temp['lst'];
	$msn_send_list_temp = $wopt->Get_web_msn_sendlog($def_shop_id,1,10);
	$msn_send_list=$msn_send_list_temp['lst'];
	$day=date("d",time());
	$company_list= $wopt->Get_express_company_by_day($def_shop_id,$day);
	$company_name=array();
	
	foreach ($company_list as $key => $value) {
		array_push($company_name,$value['name']);
	}

	$json_company_name=json_encode($company_name,JSON_UNESCAPED_UNICODE);
	$json_company_data=json_encode($company_list,JSON_UNESCAPED_UNICODE);
	

	// if (!$safe->is_view_allowed())
	// {
	// 	message("访问无权限",$this->createweburl('pc_index'),'error');
	// }
	

	// 统计表中取数据
	// 
	




	
	include $this->template('pc/main');  
	
?> 
<?php 

	global $_GPC, $_W;
	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();
	$nav = $wopt->page_info();

	$year = 2018;
	$month = 10;

	$Stype=$_GPC['stype'];

	if ($Stype==1)
	{
		$s_wx_notice= $wopt->Get_statistics_by_month_total($year,$month,'s_wx_notice');
		$s_message= $wopt->Get_statistics_by_month_total($year,$month,'s_message');
		foreach ($s_wx_notice as $key => $value) {
			$list[$key]['wx_out']=$value;
		}
		foreach ($s_message as $key => $value) {
			$list[$key]['sms_out']=$value;
		}
		$s_date =array();
		foreach ($s_wx_notice as $key => $value) {
			array_push($s_date,($key+1));
		}
		$json_s_wx_notice= json_encode($s_wx_notice);
		$json_s_message= json_encode($s_message);
		$json_s_date= json_encode($s_date);
		
		$shop_total= $wopt->Get_statistics_by_shop_total($year,$month,'s_total_notice');
		$shop_x=array();
		$shop_y=array();
		foreach ($shop_total as $key => $value) {
			$shop_total[$key]['shop_name'] =$wopt->Get_shop_name($value['s_shop_id']);
			array_push($shop_x,$shop_total[$key]['shop_name']);
			array_push($shop_y,$shop_total[$key]['cnt']);
		}
		$json_shop_x= json_encode($shop_x);
		$json_shop_y= json_encode($shop_y);
	}

	if ($Stype==2)
	{
		$s_wx_in= $wopt->Get_statistics_by_month_total($year,$month,'s_wx_in');
		$s_sms_in= $wopt->Get_statistics_by_month_total($year,$month,'s_sms_in');
		foreach ($s_wx_in as $key => $value) {
			$list[$key]['wx_in']=$value;
		}
		foreach ($s_sms_in as $key => $value) {
			$list[$key]['sms_in']=$value;
		}
		$s_date =array();
		foreach ($s_wx_in as $key => $value) {
			array_push($s_date,($key+1));
		}
		$json_s_wx_in= json_encode($s_wx_in);
		$json_s_sms_in= json_encode($s_sms_in);
		$json_s_date= json_encode($s_date);
		$shop_total= $wopt->Get_statistics_by_shop_total($year,$month,'s_in');
		$shop_x=array();
		$shop_y=array();
		foreach ($shop_total as $key => $value) {
			$shop_total[$key]['shop_name'] =$wopt->Get_shop_name($value['s_shop_id']);
			array_push($shop_x,$shop_total[$key]['shop_name']);
			array_push($shop_y,$shop_total[$key]['cnt']);
		}
		$json_shop_x= json_encode($shop_x);
		$json_shop_y= json_encode($shop_y);
	}


	include $this->template('pc/pc_statistics');  
	
	
	
?>
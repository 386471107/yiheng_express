<?php 

	global $_GPC, $_W;
  	$title = '默认网点选择';
	$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';
	$express_level_recode="yiheng_express_level_recode";
	$express_area="yiheng_express_area";
	$express_member_bind="yiheng_express_member_bind";

	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';
	$member = new yh_member();
	$opt  = new yh_opt();
	$mc_info=mc_oauth_userinfo();
	
	$openid=$_W['openid'];
	$member->judge_db_member($openid);
	$member->Update_member_status($openid,$follow=$mc_info['subscribe'],$page="area_default");
	if (empty($openid)) die();


	
 	
	$m_item=$member->Get_member($openid);


	$shop_list = $member->Get_my_shop_list($openid);

	foreach ($shop_list as $key => $value) {
		$surplus_total =$opt-> Get_sms_surplus($value['area_code'],$openid);
		$shop_list[$key]['sms_surplus']=$surplus_total['sms_surplus'];
	}
	

	include $this->template('area_default');  
	exit();

	 
	   function result_back($flag,$params =null)
		  {
		  	if (!empty($params))
		  	{
		  		foreach ($params as $key => $value) {
		  			$res[$key]=$value;	
		  		}
		  	}

		  	$res['success']=$flag;			
			$re=json_encode($res);
			exit($re);
		  }
 ?>
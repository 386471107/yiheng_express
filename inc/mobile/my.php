<?php 

	global $_GPC, $_W;
	$title = '我的快递';
	$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_user_device='yiheng_express_user_device';
	$express_log_login='yiheng_express_log_login';
	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';
	$member = new yh_member();
	$opt  = new yh_opt();
	$mc_info=mc_oauth_userinfo();
	
	$openid=$_W['openid'];
	$member->judge_db_member($openid);
	$member->Update_member_status($openid,$follow=$mc_info['subscribe'],$page="home");
	if (empty($openid)) die();


	$myinfo = $member->Get_member($openid);
	$has_shop= $myinfo['m_defaut_area'];

	if ($has_shop==0)
	{
		include $this->template('my'); 
		exit();
	}
	$u_shop_id = $member->Get_shop_id($openid);
	$shop_total = $opt->Get_my_shop_total($openid);
	$staff_total =$opt->Get_my_shop_staff_total($openid);
	$surplus_total =$opt-> Get_sms_surplus($u_shop_id,$openid);
	$myinfo['w_cnt'] =$opt->Get_wait_num($openid);
	
	
	$params = array(
				'd_openid' => $openid,
				'uniacid' => $_W['uniacid'],
			);
	$condition = " where d_openid like :d_openid and uniacid =:uniacid";
	$sql = "SELECT count(*) as cnt  from ".tablename($express_user_device).$condition;
	$item = pdo_fetch($sql,$params);
	
	
	if (intval($item['cnt'])==0)
	{
		$agent=$_SERVER['HTTP_USER_AGENT']; 
		$data = array(		 
					'uniacid' => $_W['uniacid'],
					'd_agent' => $agent,
					'd_device' =>$opt->getDevice($agent),
					'd_brand' =>$opt->getbrand($agent),
					'd_time' => time(),
					'd_openid' => $openid,
					
					);
		$result=pdo_insert($express_user_device, $data);
	}
	
			
	

	
	//如果存在，逐个遍历。或者描扫判断，暂不做2018.9.10
	// if (empty($myinfo['m_defaut_area']))
	// {
	// 	$shop_info = $member->Get_shop_sence_all($openid);
	// 	$sence_list=array();
	// 	$code_list=array();
	// 	foreach ($shop_info as $key => $value) {
	// 		$exsit = $member->Get_shop_id_by_sence($value['scene_str']);
	// 		if (!empty($exsit))
	// 		{
	// 			array_push($sence_list,$value['scene_str']);
	// 			array_push($code_list,$exsit['area_code']);
	// 			$default_area =$exsit['area_code'];
	// 		}
			
	// 	}
	// 	$sence = json_encode($sence_list);
	// 	$code = json_encode($code_list);
	// 	$data=array(
	// 		'm_sence_list' => $sence,
	// 		'm_area_list' => $code,
	// 		'm_defaut_area' => $default_area,
	// 		);
	// 	$result = pdo_update($express_member, $data, array('id' => $myinfo['id']));
	// }


	
	// $myinfo['total']=$opt ->Get_staff_total($member->Get_shop_id($openid));
	// $shop_total_arr=json_decode($myinfo['m_area_list']);
	// $shop_total =sizeof($shop_total_arr);
	// 
	// $sms_item= $opt->Get_shop_msm($shop_id=$member->Get_shop_id($openid),$openid,$myinfo['m_level']); 
	
	include $this->template('my');  
	exit();

 ?>
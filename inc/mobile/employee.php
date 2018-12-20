<?php 
	global $_GPC, $_W;

	include_once MODULE_ROOT.'/inc/mobile/common.php';
	$title = '快递员后台管理';
	//走权限管理
	$page="employee";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理
	
	
	
	$yiheng_express_employee='yiheng_express_employee';
	$params = array(
			'e_openid' => $openid,
			'uniacid' => $_W['uniacid'],
		);
	$condition = " where e_openid like :e_openid and uniacid =:uniacid";
	$sql = "SELECT * from ".tablename($yiheng_express_employee).$condition;
	$item = pdo_fetch($sql,$params);
	
	
	if ($_W['isajax'])
		{
			$act= $_GPC['act'];
			if ($act=="update_laction")
			{	
				$lat=$_GPC['lat'];
				$lng=$_GPC['lng'];
				if ($lat && $lng)
				{
					$key = 'N2MBZ-JMYWX-Q6Q46-ZRK4X-RQB4H-PTB4N';
					$url="https://apis.map.qq.com/ws/geocoder/v1/?location=".$lat.",".$lng."&key=".$key."&get_poi=0";			
					$addr_info_json = $opt->curlGet($url);
					$addr_info=json_decode($addr_info_json);
					$data= array(
								'e_lat' =>$lat,
								'e_lng' =>$lng,
								'e_address' =>$addr_info->result->address_component->district.",".$addr_info->result->address_component->street,
								'e_local_address' =>$addr_info->result->address,
								'e_update_loc_time' =>time(), 
							);
					$result = pdo_update($yiheng_express_employee, $data,array('e_id'=>$item['e_id']));	
					
					if($result)
					result_back(1,$data);   
				}
				result_back(0);				
				
			}
			
			if ($act=="laction_att")
			{	
				$express_recode_loc_att="yiheng_express_recode_loc_att";
				$lat=$_GPC['lat'];
				$lng=$_GPC['lng'];
				$r_id=intval($_GPC['r_id']);
				if ($lat && $lng && $r_id)
				{
					$data= array(
								'la_lat' =>$lat,
								'la_lng' =>$lng,
								'la_r_id' =>$r_id,
								'la_openid' =>$openid,
								'la_ip' =>$_W['clientip'],
								'la_addtime' =>time(), 
								'uniacid' => $_W['uniacid'], 
							);
					$result = pdo_insert($express_recode_loc_att, $data);
				}
				result_back(0);
			}
		}
	
	
	
	
	
	$shop = $member->Get_shop_name($myinfo['m_defaut_area']);
	include $this->template('employee');
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
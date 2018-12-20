<?php 

	global $_GPC, $_W;
	$title = '网点列表';
	$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';
	$express_level_recode="yiheng_express_level_recode";
	$express_area="yiheng_express_area";
	$express_member_bind="yiheng_express_member_bind";
	$express_user_loction="yiheng_express_user_loction";

	
	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';
	$member = new yh_member();
	$opt  = new yh_opt();
	$mc_info=mc_oauth_userinfo();
	
	$openid=$_W['openid'];
	$member->judge_db_member($openid);
	$member->Update_member_status($openid,$follow=$mc_info['subscribe'],$page="home");
	if (empty($openid)) die();

	$key = 'N2MBZ-JMYWX-Q6Q46-ZRK4X-RQB4H-PTB4N';

	if ($_W['isajax'])
		{
			
			$act= $_GPC['act'];
			$id= (int)($_GPC['id']);
			if ($act=="update_laction")
			{	
				$lat=$_GPC['lat'];
				$lng=$_GPC['lng'];
				if ($lat && $lng)
				{
					$time_flag = time()-86400;
					$d_pramas=array(
							'uniacid' => $_W['uniacid'], 
							'loc_openid' => $openid,
							'loc_addtime' => $time_flag,
							
						); 
						$where  =" where loc_openid like :loc_openid and loc_addtime > :loc_addtime and uniacid=:uniacid";
						$item = pdo_fetch('select count(*) as cnt  from ' . tablename($express_user_loction) . $where, $d_pramas);
						if ($item['cnt']==0)
						{
							$udate=array(
										'm_lat' => $lat,
										'm_lng' =>$lng,
									);
							$res = pdo_update($express_member,$udate,array('m_openid like' => $openid,'uniacid' => $_W['uniacid']));			
							$url="https://apis.map.qq.com/ws/geocoder/v1/?location=".$lat.",".$lng."&key=".$key."&get_poi=1";			
							$addr_info_json = $opt->curlGet($url);
							$addr_info=json_decode($addr_info_json);
							$data= array(
										'loc_lat' =>$lat,
										'loc_lng' =>$lng,
										'loc_address' =>$addr_info->result->address,
										'loc_recommend' =>$addr_info->result->formatted_addresses->recommend,
										'loc_adcode' =>$addr_info->result->ad_info->adcode,
										'loc_province' =>$addr_info->result->address_component->province,
										'loc_city' =>$addr_info->result->address_component->city,
										'loc_district' =>$addr_info->result->address_component->district,
										'loc_street' =>$addr_info->result->address_component->street,
										'loc_street_number' =>$addr_info->result->address_component->street_number,
										'loc_pois_title' =>$addr_info->result->pois[0]->title,
										'loc_pois_address' =>$addr_info->result->pois[0]->address,
										'loc_pois_category' =>$addr_info->result->pois[0]->category,
										'loc_openid' =>$openid,
										'loc_addtime' =>time(), 
										'uniacid' => $_W['uniacid'], 
									);
							$result = pdo_insert($express_user_loction, $data);	
					}
				}			
			exit();	
				
				
			}
			if ($act=="join" && $id )
			{								
				$m_item=$member->Get_member($openid);
				if($m_item['m_defaut_area']==0)
				{
					//更新bind表中数据
					$pramas=array(
							'uniacid' => $_W['uniacid'], 
							'area_code' => $id,
							'area_scan_only' => 0,
						); 
						$where  =" where area_code = :area_code and area_scan_only =:area_scan_only and uniacid=:uniacid";
						$item = pdo_fetch('select *  from ' . tablename($express_area) . $where, $pramas);
						$data=array(
							'm_defaut_area' => $id, 
						); 
						if (!empty($item))
						{
							$resultA = pdo_update($express_member, $data, array('id' => $m_item['id']));
							$pramas=array(
								'uniacid' => $_W['uniacid'], 
								'bind_shop_id' => $id,
								'bind_m_openid' =>$openid,
								); 
								$where  =" where bind_m_openid =:bind_m_openid and bind_shop_id = :bind_shop_id and uniacid=:uniacid";
								$b_item = pdo_fetch('select *  from ' . tablename($express_member_bind) . $where, $pramas);
								if (empty($b_item))
								{
									$b_data= array(
										'bind_shop_id' =>$id,
										'bind_m_uid' => $m_item['m_uid'],
										'bind_m_openid' => $m_item['m_openid'],
										'bind_default' => 1,
										'bind_time' => time(),
										'uniacid' => $_W['uniacid'],
									);			
									$b_result = pdo_insert($express_member_bind, $b_data);
								}
								$res = pdo_delete($express_member_bind,array('bind_m_openid like' => $openid,'bind_shop_id' =>0));
								
								$params['tips']='成功选择店铺!';
								$params['sign']='success';
								result_back(1,$params);
						}

				}
				else
				{
					if($m_item['m_defaut_area']==$id)
					{
						$params['tips']='无需更改！';
						$params['sign']='cancel';
						result_back(1,$params);
					}
					else
					{
						$pramas=array(
							'uniacid' => $_W['uniacid'], 
							'area_code' => $id,
							'area_scan_only' => 0,
						); 
						$where  =" where area_code = :area_code and area_scan_only =:area_scan_only and uniacid=:uniacid";
						$item = pdo_fetch('select *  from ' . tablename($express_area) . $where, $pramas);
						$data=array(
							'm_defaut_area' => $id, 
						); 

						if (!empty($item))
						{
							
							
							$pramas=array(
								'uniacid' => $_W['uniacid'], 
								'bind_shop_id' => $id,
								'bind_m_openid' =>$openid,
								); 
								$where  =" where bind_m_openid =:bind_m_openid and bind_shop_id = :bind_shop_id and uniacid=:uniacid";
								$b_item = pdo_fetch('select *  from ' . tablename($express_member_bind) . $where, $pramas);
								if (empty($b_item))
								{
									$b_data= array(
										'bind_shop_id' =>$id,
										'bind_m_uid' =>$m_item['m_uid'],
										'bind_m_openid' => $m_item['m_openid'],
										'bind_default' => 1,
										'bind_time' => time(),
										'uniacid' => $_W['uniacid'],
									);

									$res = pdo_update($express_member_bind,array('bind_default' =>0),array('bind_m_openid like' => $openid,'uniacid' => $_W['uniacid']));
									$b_result = pdo_insert($express_member_bind, $b_data);

									$data['m_level']=0;

								}
								else
								{
									$data['m_level']=$b_item['bind_m_level'];
								}

								$resultA = pdo_update($express_member, $data, array('id' => $m_item['id']));
								$params['tips']='成功选择店铺!';
								$params['sign']='success';
								result_back(1,$params);
						}
					}
				}
				


			}
			exit();



		
			$set_res=$opt->set_default_shop($id,$openid);

			if ($set_res)
			{	
				result_back(1,$res);	
			}
		}


	

	$myinfo=$member->Get_member($openid);
	$shop_list = $member->Get_shop_list_all();

	include $this->template('my_area');  
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
<?php 

		global $_GPC, $_W;


		include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
		$wopt = new yh_web_option();
		$nav = $wopt->page_info();

	



		$express_area='yiheng_express_area';
		$express_member='yiheng_express_member';
		$express_sms_info='yiheng_express_sms_info';
		$express_member_bind='yiheng_express_member_bind';
		$yiheng_express_employee='yiheng_express_employee';

		

		$Tea=tablename($express_area);
		$Tem=tablename($express_member);
		$Tesi=tablename($express_sms_info);
		$Temb=tablename($express_member_bind);


		$act=trim($_GPC['act']);
		$id=(int)($_GPC['id']);
		

		if ($act=='cancel')
		{
			$b_uid=(int)($_GPC['b_uid']);
			$bind_shop_id=(int)($_GPC['bind_shop_id']);
			//取消权限
			$res =pdo_update($express_member_bind,array('bind_m_level' =>0),array('bind_m_uid' =>$b_uid));
			$resa =pdo_update($express_member,array('m_level' =>0),array('m_uid' =>$b_uid));
			
			$params = array(
					'sms_uid' => $b_uid,
					'sms_shop_id' => $bind_shop_id,
					);
			$sms_sql ="select * from $Tesi where sms_uid =:sms_uid and sms_shop_id =:sms_shop_id";
			$smsinfo = pdo_fetch($sms_sql,$params);
			//当数据大于0时，归还短信新闻稿店铺
			if ($smsinfo['sms_surplus']>0)
			{
				pdo_update($express_area, array('area_last_message +=' => $smsinfo['sms_surplus']), array('area_code' => $bind_shop_id));
			}
			
			
			$resb = pdo_delete($express_sms_info, array('sms_uid' => $b_uid,'sms_shop_id'=>$bind_shop_id));

			if ($res && $resa && $resb)
			{
				message('权限取消成功', referer(), 'success');
			}
			else
			{
				message('权限取消失败，请检查');
			}

			
		}


		if ($_W['isajax'] && 0)
		{
			
			$area_code=(int)($_GPC['area_code']);
			$id=(int)($_GPC['id']);
			if ($act=='get_msg' && $area_code && $id)
			{
				$params = array(
								'sms_m_uid' => $id,
								'sms_shop_id' => $area_code,
								);
				$where=' WHERE sms_m_uid = :sms_m_uid and sms_shop_id =:sms_shop_id ';	
				$orderby='';
				$limit =" limit 1";
				$sms_info = pdo_fetch("SELECT *  FROM ".tablename($express_sms_info). $where.$orderby,$params);


				if (!empty($sms_info))
				{
 
				//会员基本信息表，如姓名之类的字段	
				 $params = array(
							'm_openid' => $sms_info['sms_openid_cando'],
							);
				 $where = " where m_openid like :m_openid ";	
				 $member_info = pdo_fetch("SELECT *  FROM ".tablename($express_member). $where,$params);
				//会员基本信息表，如姓名之类的字段	

				 $params = array(
							'bind_m_openid' => $sms_info['sms_openid_cando'],
							'bind_shop_id' => $area_code,
							);


				 $where = " where bind_m_openid like :bind_m_openid  and bind_shop_id = :bind_shop_id";	
				 $level_info = pdo_fetch("SELECT *  FROM ".tablename($express_member_bind). $where,$params);
					$res = array(
								'sms_surplus' => $sms_info['sms_surplus'],
								'sms_status' =>$sms_info['sms_status'],
								'allow_notice' =>$member_info['m_allow_notice'],
								'ban' =>$member_info['m_ban'],
								'level' =>$level_info['bind_m_level'],
								
								);


				result_back(1,$res);
				}
				else
				{
					result_back(0);
				}

			}

			exit();

		}	
		
		
		
		if(checksubmit('ep_save'))
		{
			$id = (int)($_GPC['id']);
			
			$params = array(
							'e_uid' => $id,
							'uniacid' => $_W['uniacid'],
							);
			$where=" WHERE e_uid = :e_uid  and uniacid =:uniacid";
			$item = pdo_fetch("SELECT * FROM ".tablename($yiheng_express_employee).$where,$params);
		
			if (empty($item))
			{
				$data=array(
				'e_openid' =>$wopt->Get_openid_by_uid($id),
				'e_send_day_max'=>intval($_GPC['e_send_day_max']),
				'e_f_user'=>intval($_GPC['e_f_user'])==1?1:0,
				'e_f_loc'=>intval($_GPC['e_f_loc'])==1?1:0,
				'e_status'=>intval($_GPC['e_status'])==1?1:0,
				'uniacid' => $_W['uniacid'],
				'e_uid'=>$id,
				);
				$result=pdo_insert($yiheng_express_employee, $data);
					
			}
			else
			{
				$data=array(	
				'e_send_day_max'=>intval($_GPC['e_send_day_max']),
				'e_f_user'=>intval($_GPC['e_f_user'])==1?1:0,
				'e_f_loc'=>intval($_GPC['e_f_loc'])==1?1:0,
				'e_status'=>intval($_GPC['e_status'])==1?1:0,
				'uniacid' => $_W['uniacid'],
				'e_uid'=>$id,
				);
				$result=pdo_update($yiheng_express_employee, $data,array('e_uid'=>$item['e_uid']));
				
			}
			if ($result) message('修改店员信息成功', referer(), 'success');
			message('信息修改失败');
		
			
			
			
		}
		if(checksubmit('m_save'))
		{

		
			$id = (int)($_GPC['id']);

			
			//当前店铺=修改店铺时，需要同时把此埏权限更改，如果！=，则不需更改当前level 
         	$m_data=array(
				'm_realname'=>trim($_GPC['m_realname']),
				'm_tel'=>trim($_GPC['m_tel']),
				'm_remark'=>trim($_GPC['m_remark']),
			
			);
			
			$m_info =$wopt->Get_member_info_by_id($id);
         	$bind_shop_id = intval($_GPC['bind_shop_id']);
			
			if($m_info['m_defaut_area']==$bind_shop_id)
			{
				$m_data['m_level']=(int)($_GPC['m_level']);
			}
 			$smsinfo = $wopt->Get_web_sms_info($id,$bind_shop_id);

			
			
			//当为空时，需要insert
			
			
 			if (!$bind_shop_id)
 				message("店铺代码不正确！");
 			$last_sms_surplus=$smsinfo['sms_surplus'];
 			
        	 if ($id)
        	 {	
				$b_m_data=array(
				'bind_m_level'=>(int)($_GPC['m_level']),
				'bind_m_ban'=>trim($_GPC['m_ban']),
				
			);
			 
			
		 
        	 	//start权限分配 -更新member表中信息，同时需更新member_bind表。
        	 	 $m_res =pdo_update($express_member,$m_data,array('m_uid' =>$id,'uniacid' => $_W['uniacid']));
        	 	 $m_resa =pdo_update($express_member_bind,$b_m_data,array('bind_shop_id'=>$bind_shop_id,'bind_m_uid' =>$m_info['m_uid'],'uniacid' => $_W['uniacid']));
				//end权限分配 -更新member表中信息，同时需更新member_bind表。
        	 	
        	 	$smsinfo=$wopt->Get_web_sms_info($m_info['m_uid'],$bind_shop_id);
        	 	
        	 	if (empty($smsinfo))
        	 	{
        	 		$wopt->Insert_web_sms_info($m_info['m_openid'],$m_info['m_uid'],$bind_shop_id);
					$smsinfo = $wopt->Get_web_sms_info($id,$bind_shop_id);
        	 	}
        	 	
        	 	//判断接收来的短信数量
        	 	if ((int)($_GPC['sms_surplus'])<0)
        	 	{
        	 		message('请输入正确的短信量');
        	 	}

        	 	//原有数据减传过来的数据,如果不为零，则进入修改+log记录
        	 	$change_total = $smsinfo['sms_surplus']-(int)($_GPC['sms_surplus']) ;
				
        	 	if ($change_total !=0)
        	 	{
        	 		if ($change_total>0)
		        	 	{
		        	 		//会员数据大于当前，则为减少数据，店铺增加数据
		        	 		$res = $wopt->Change_shop_sms_total($smsinfo['sms_shop_id'],$change_total,1);
		        	 		$chg_type=0;
						
		        	 	}
		        	 	else
						{
							$res =$wopt->Change_shop_sms_total($smsinfo['sms_shop_id'],$change_total,0);
							$chg_type=1;
							
						}
					 if (!$res) message('店铺短信数不正确，短信修改数据不正确，处理失败');
					 $wopt->Web_sms_insert_change($_W['username'],$_W['uid'],$smsinfo['sms_shop_id'],$smsinfo['sms_openid_cando'],$smsinfo['sms_uid'],(int)($_GPC['sms_surplus']),-$change_total,$last_sms_surplus,$chg_type);
        	 	}
        	 	
				 

        	 	if (empty($smsinfo))
        	 	{
					//上面已插入，些处不会进入
        	 			$sms_data=array(	
						'sms_uid'=>$id,
						'sms_surplus'=>(int)($_GPC['sms_surplus']),
						'sms_shop_id'=>$bind_shop_id ,
						'sms_openid_cando'=>$m_info['m_openid'],
						'sms_type'=>trim($_GPC['m_level']),
						'sms_status'=>$_GPC['sms_status'] ,
						'sms_notice'=>$_GPC['sms_notice'] ,
						'sms_level'=>(int)($_GPC['m_level']),
						'uniacid' => $_W['uniacid'],
						'sms_m_uid'=>$id,
						);
					// $result=pdo_insert($express_sms_info, $sms_data);

        	 	}
        	 	else
        	 	{
    	 			$sms_data=array(
    	 				'sms_surplus'=>(int)($_GPC['sms_surplus']),
						'sms_type'=>trim($_GPC['m_level']),
						'sms_status'=>$_GPC['sms_status'] ,
						'sms_notice'=>$_GPC['sms_notice'] ,
						'sms_level'=>(int)($_GPC['m_level']),
					);
					$result = pdo_update($express_sms_info, $sms_data, array('sms_id' => $smsinfo['sms_id'],'uniacid' => $_W['uniacid']));

        	 	}

 
        	 		if ($m_res || $m_resa || $result) {
					message('修改店员信息成功', referer(), 'success');
					} else {
					message('修改店员信息失败，请重试');
					}
					exit();
        	 }
        	

	
		}



		

		 

		if ($id)
		{
			$def_shop_id = $wopt->Get_web_def_shop();
			$params = array(
							'bind_m_uid' => $id,
							'uniacid' => $_W['uniacid'],
							'bind_shop_id' => $def_shop_id,
							);
			$leftjoin=" LEFT JOIN $Tem on $Temb.bind_m_uid = $Tem.m_uid ";
			$where=" WHERE $Temb.bind_m_uid = :bind_m_uid and bind_shop_id =:bind_shop_id and $Temb.uniacid =:uniacid";	
			$orderby='';
			$limit =" limit 1";
			$item = pdo_fetch("SELECT * FROM $Temb".$leftjoin .$where.$orderby,$params);
			
			$paramsa = array(
							'e_uid' => $id,
							'uniacid' => $_W['uniacid'],
							);
			$where=" WHERE e_uid = :e_uid  and uniacid =:uniacid";
			$emp = pdo_fetch("SELECT * FROM ".tablename($yiheng_express_employee).$where,$paramsa);
			
			
			if (!empty($item))
			{
				$item['m_level'] =  $wopt->Get_web_member_level_by_bind($item['bind_m_uid'],$def_shop_id);
				$s_info = $wopt->Get_web_sms_info($item['bind_m_uid'],$def_shop_id);
				$item['sms_surplus']=$s_info['sms_surplus'];
				$item['sms_status']=$s_info['sms_status'];
				$item['shop_name']= $wopt->Get_shop_name($item['bind_shop_id']);
			}
			else
			{
				message('无权限修改此用户信息！',"error");
			}
		
		}
		else
		{
			message('参数不正确，请重试！',"error");
		}

		
		// $where=' WHERE uniacid = :uniacid ';	
		// $orderby='';	
		//    $params = array(
		// 'uniacid' => $_W['uniacid'],								
		// );
		//    $sql = "SELECT * from ".$Tea.$where;
		// $area_list = pdo_fetchall($sql,$params);


		
	include $this->template('pc/pc_member_edit');  
	
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
<?php 

	global $_GPC, $_W;
	$title = '新用户绑定';
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
	$member->Update_member_status($openid,$follow=$mc_info['subscribe'],$page="home");
	if (empty($openid)) die();


	if(!$member->Get_shop_id($openid))
	{
		message('点击选择所属网点!',$this->createMobileUrl('my_area'),'warning');
	}


	$act = $_GPC['act'];
		{
			if($act=='rebind')
			$title = '用户重新绑定';
		}

 

if ($_W['isajax'])
		{

	
		$act=trim($_GPC['act']);
		$reg_tel=trim($_GPC['tel']);


		if ($act=="register")  
		{
			
			//检测是否属于店铺需要？定为不需要，先注册再选店铺也可行
			//检测短信配置，

			$send_per_time=60;//单次发送间隔
			$send_per_cnt=3;//最大申请次数
			$send_total_time=7200;//次数达最大值后复位时间


			$shop_id = $member->Get_shop_id($openid);


			if(!$shop_id)
				{
					$params['tips']='选择网点后重试！';
					$params['sign']='cancel';
					result_back(4,$params);
				}


			//判断管于是员短信数。如果小于则无法注册
			//
			//
			$manage_surplus = $opt->Get_sms_surplus($shop_id);

			if ($manage_surplus['sms_surplus']>0 && $manage_surplus['sms_status']==1)
			{
				$goto_next=1;
			}
			else
			{
				$params['tips']='无法注册，联系管理员！';
				$params['sign']='cancel';
				result_back(4,$params);
			}

			// 判断号码存在
			$tel_exsit = $member->Get_tel_exist($reg_tel) ;
			if($tel_exsit)
			{
				$params['tips']='号码已存在';
				$params['sign']='cancel';
				result_back(4,$params);
			} 

			//判断号码是否超次
			$reg_item = $opt->Get_reg_info($openid);
			
			
			if (empty($reg_item))
			{
				$reg_cnt=1;
				$rand_code=rand(1000,9999);
				$opt->Insert_reg_info($openid,$rand_code,$reg_tel);
				$opt->Insert_reg_info_log($openid,$rand_code,$reg_tel,$reg_cnt) ;
				


				$opt->send_sms($openid,$reg_tel,$shop_id,$rand_code,$sms_type=1);

				$opt->Uptdate_sms_surplus($manage_surplus['sms_id'],$manage_surplus['sms_used']+1,$manage_surplus['sms_surplus']-1);


				$params['tips']='发送成功';
				$params['sign']='success';
				result_back(1,$params);
				
			}
			else
			{
				
				$rand_code=$reg_item['reg_code'];
				$reg_lasttime=$reg_item['reg_lasttime'];
				$reg_cnt=$reg_item['reg_cnt'];
			}

			//当间隔小于设置时间时，退出并提示
			$timediff=time()-$reg_lasttime;
			if ($timediff<$send_per_time)
			{
				$params['tips']='申请过快';
				$params['sign']='forbidden';
				result_back(4,$params);
			}
			//超总时后计数从1开始
			  
			if ($timediff>$send_total_time)
			{
				$data = array(
				'reg_cnt' => 0,	
				); 
				$result = pdo_update($express_reg, $data, array('id' => $reg_item['id']));
				$reg_cnt=1;
			}	

			
			//当注册次数大于设置数


			if ($reg_cnt>$send_per_cnt)
			{
				$params['tips']='时间段内超次';
				$params['sign']='forbidden';
				result_back(4,$params);
			}

				 
				$data = array(
						'reg_cnt'=> $reg_cnt+1,
						'reg_lasttime'=> time(),
						'reg_tel'=> $reg_tel,
						);
				$result = pdo_update($express_reg, $data, array('id' => $reg_item['id']));
				$opt->Insert_reg_info_log($openid,$rand_code,$reg_tel,$reg_cnt+1) ;


			
				$opt->send_sms($openid,$reg_tel,$shop_id,$rand_code,$sms_type=1);	
				$opt->Uptdate_sms_surplus($manage_surplus['sms_id'],$manage_surplus['sms_used']+1,$manage_surplus['sms_surplus']-1);
				$params['tips']='发送成功';
				$params['sign']='success';
				result_back(1,$params);
				exit();	
		}

 
		 if ($act=='bind')
			
			
		{
			$_SESSION['bind']=$_SESSION['bind']==""?1:$_SESSION['bind'];
			if 	($_SESSION['bind'] >50)
			{
				$params['tips']='过会再绑吧！';
				$params['sign']='cancel';
				result_back(0,$params);
			}

			$reg_tel=empty($_GPC['tel'])?exit():trim($_GPC['tel']);
			$code=empty($_GPC['code'])?exit():$_GPC['code'];
			
			// 判断号码存在
			$params=array(
			":reg_code" => $code,
			":reg_tel" => $_GPC['tel'],
			":uniacid" => $_W['uniacid'],
			);
			$where=' WHERE reg_tel = :reg_tel and reg_code = :reg_code  and uniacid = :uniacid ';
			$regitem = pdo_fetch("SELECT *  FROM ".tablename($express_reg). $where,$params);

			
			if ($regitem)
			{
			$data = array(
					'm_tel' => $reg_tel,					
					);

			$result = pdo_update($express_member, $data,  array('m_openid like ' =>$openid,'uniacid' => $_W['uniacid']));

			$result = pdo_delete($express_reg, array('uniacid' => $_W['uniacid'],'reg_tel' =>  $reg_tel));
				
				$params['tips']='绑定成功';
				$params['sign']='success';
				result_back(1,$params);
			}		
			else
			{	
				$_SESSION['bind']++;
				$params['tips']='验证码错误';
				$params['sign']='cancel';
				result_back(0,$params);

			}
			
		}




	}



	include $this->template('register');
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
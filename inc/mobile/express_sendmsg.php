<?php 

	global $_GPC, $_W;
	$title = '快递后台发送端';
	include_once MODULE_ROOT.'/inc/mobile/common.php';
	
	
	//走权限管理
	$page="express_sendmsg";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理
	


	
	// print_r($_SESSION);
	include_once MODULE_ROOT.'/inc/func/yh_kd_curl.php';
	$kd_curl = new yh_kd_curl();
	
	if (1)
	{
		if ($_W['isajax'])
		{
			$act=trim($_GPC['act']);
			if ($act=='del')
			{
				$delid=(int)$_GPC['id'];
				$result = pdo_delete($express_recode, array('id' => $delid));
				$res['success']=1;			
				$re=json_encode($res);
				exit($re);
			}
			
			$sid =intval($_GPC['sid']);
			if ($act == 'save' && $sid )
			{
				$udata = array(
						'tl_uname' => trim($_GPC['sname']),
						'tl_area' =>  trim($_GPC['sarea']),
						'tl_addr' =>  trim($_GPC['saddr']),
						'tl_modify_ex' =>  1,
						); 
				$result = pdo_update($express_tel_list, $udata,array('id' => $sid));

				if ($result)
				{
					result_back(1);
				}
			}
			
			
			if ($act=='modify')
			{
				$modify_id=(int)$_GPC['id'];
				$leftjoin =" left join ".tablename($express_tel_list)." on ".tablename($express_recode).".recoder_tel=".tablename($express_tel_list).".m_tel";
				$item_where= ' where '.tablename($express_recode).'.id =:id and '.tablename($express_recode).'.uniacid=:uniacid';
				$tel_params = array(
				'uniacid' => $_W['uniacid'],
				'id' =>$modify_id,
				);  
				$item_info_all = pdo_fetch("SELECT * FROM ".tablename($express_recode).$leftjoin. $item_where,$tel_params);	
				
				$item_info['id']=$item_info_all['id'];
				$item_info['tl_uname']=$item_info_all['tl_uname'];
				$item_info['tl_area']=$item_info_all['tl_area'];
				$item_info['tl_addr']=$item_info_all['tl_addr'];
				
				
			
			
				$item_info['success']=1;			
				$re=json_encode($item_info);
				exit($re);
			}

			if ($act=='fill_tel')
			{
					$end_no = intval($_GPC['no']);
					$tel_where= ' where tl_tel_end_no =:tl_tel_end_no and uniacid=:uniacid';
					$tel_params = array(
					'uniacid' => $_W['uniacid'],
					'tl_tel_end_no' =>$end_no,
					);  
					$tel_info = pdo_fetch("SELECT *  FROM ".tablename($express_tel_list). $tel_where,$tel_params);
					if (!empty($tel_info))
					{	
						$res['tel']=$tel_info['m_tel'];
						$res['dname']=$tel_info['tl_uname'];
						$res['darea']=$tel_info['tl_area'].$tel_info['tl_addr'];
						result_back(1,$res);

					}
					result_back(0);

			}
			
			if ($act=='get_info')
			{
					$m_tel = intval($_GPC['no']);
					$tel_where= ' where m_tel =:m_tel and uniacid=:uniacid';
					$tel_params = array(
					'uniacid' => $_W['uniacid'],
					'm_tel' =>$m_tel,
					);  
					$tel_info = pdo_fetch("SELECT *  FROM ".tablename($express_tel_list). $tel_where,$tel_params);
					if (!empty($tel_info))
					{	
						$res['tel']=$tel_info['m_tel'];
						$res['dname']=$tel_info['tl_uname'];
						$res['darea']=$tel_info['tl_area'].$tel_info['tl_addr'];
						result_back(1,$res);

					}
					result_back(0);

			}
 
			//标记未发时动作, 如果未发则标记
			if ($act=='senddone')
			{
					if($member->is_shop_manage($openid))
					{
						$udata = array(
						'recoder_send_status' => 1,
						'recoder_done_by' => '管理员标记，未发送',
						'recoder_senttime' => time(),
						); 
						$result = pdo_update($express_recode, $udata,array('recoder_send_status' => 0,'recoder_shop_id' => $member->Get_shop_id($openid),'uniacid' =>$_W['uniacid']));
					}
					else
					{
						$udata = array(
						'recoder_send_status' => 1,
						'recoder_done_openid' => $openid,
						'recoder_done_by' => '管理员标记，未发送',
						'recoder_senttime' => time(),
						); 
						$result = pdo_update($express_recode, $udata,array('recoder_send_status' => 0,'recoder_add_openid' =>$openid,
							'recoder_shop_id' =>$member->Get_shop_id($openid),'uniacid' =>$_W['uniacid']));
					}

					return $result;
				
			}


			if ($act=='sending')
			{
					$type=trim($_GPC['type']);

					if ($type=="weixin" || $type=="msm"  )
					{ 
 
							if ($type=="weixin")
							{

								//检测微信发送条数
								$u_shop_id=	$member->Get_shop_id($openid);
								
								$weixin_last_total = $opt->Get_today_weixin_surplus($u_shop_id);

								if ($weixin_last_total<=0)
								{
									result_back(-1);
								}

								$item = $opt->Get_weixin_wait_item($openid);

									if(!empty($item))
									{
										// $tplinfo =$opt->Get_tplinfo($u_shop_id);
										$tplwhere=" where uniacid =:uniacid and tpl_status =:tpl_status and tpl_shop_id =:tpl_shop_id";	
										$tplparams = array(
										'uniacid' => $_W['uniacid'],
										'tpl_status' =>1,
										'tpl_shop_id' =>$u_shop_id,
										);  
										$tplinfo = pdo_fetch("SELECT *  FROM ".tablename($express_tpl). $tplwhere,$tplparams);
																$shop_info = $member->Get_shop_name($u_shop_id);

										$in_time=date("Y-m-d H:i",$item['recoder_create']);
										// $r_str = array($shop_info['area_name'],$item['recoder_code'],$in_time,$item['recoder_barcode']);	
										$r_str = array($shop_info['area_name'],$item['recoder_code'],$in_time,$item['recoder_barcode'],$item['recoder_shelves'].'-'.$item['recoder_goods_num']);	
										 
										 if (!empty($tplinfo)) 
										 {
											 $datainfo= array(
											 			'tpl_id' =>$tplinfo['tpl_id'],
														'first' => $opt -> Word_replace($tplinfo['tpl_title'],$r_str)."\n",
														'keyword1' => $opt -> Word_replace($tplinfo['tpl_kw1'],$r_str),
														'keyword2' => $opt -> Word_replace($tplinfo['tpl_kw2'],$r_str),
														'keyword3' => $opt -> Word_replace($tplinfo['tpl_kw3'],$r_str),
														'keyword4' => $opt -> Word_replace($tplinfo['tpl_kw4'],$r_str),
														'keyword5' => $opt -> Word_replace($tplinfo['tpl_kw5'],$r_str),
								 						'remark' => "\n".$opt -> Word_replace($tplinfo['tpl_remark'],$r_str),
								 						'size' => $tplinfo['tpl_size'],
													);


 											$wx_openid=$item['recoder_openid'];
											$wx_send_status=$opt->send_wx($wx_openid,$tplid,$datainfo);
											$opt->send_wx_log($openid,$wx_openid,$u_shop_id,$send_type=0,$wx_send_status,$error_desc='') ;
											$opt->Update_stats_send_notice($item['recoder_shop_id'],$notice_type=0);
											
											$opt->Uptdate_today_weixin_used($item['recoder_shop_id']);
											


											$res['id']=$item['id'];	 
											$udata = array(
											'recoder_send_status' => 1,
											'recoder_senttime' => time(),
											); 
											$result = pdo_update($express_recode, $udata, array('id' => $item['id']));






											result_back(1,$res);
										 }
										  else
									     {
									     	result_back(2);
									     } 
										} 
										else
										{
											result_back(0);
										}

							}
 
							if ($type=="msm")
							{
								//检查短信开关是否开启
								$shop_id=$member->Get_shop_id($openid);
								$myinfo = $member->Get_member($openid);
								$sms_cfg= $opt->Check_sms_cfg($shop_id);
								$item = $opt->Check_Sms($openid,$shop_id,$myinfo['m_level']);
								
								 
								//配置检查
								if ($sms_cfg['sms_key']=='' || $sms_cfg['sms_message_id']==0 || $sms_cfg['sms_message']=='')
								{
									$params['tips']='短信未正确配置，Err:S0x01';
									$params['sign']='forbidden';
									result_back(5,$params);
								}
								//  
								if (empty($item))
								{
									$params['tips']='管理未授权！:Err:S0x02';
									$params['sign']='forbidden';
									result_back(4,$params);
								}

								if ($item['sms_status'] !=1 || $sms_cfg['sms_do'] !=1)
								{
									$params['tips']='短信未开启！:Err:S0x03';
									$params['sign']='forbidden';
									result_back(4,$params);
								}

								
								//检查当日是否超次
								$sms_cur_total =  $opt->Get_today_total_send($shop_id,1);

								if ($sms_cur_total>$sms_cfg['sms_day_max'])
								{
									$params['tips']='当日超次,请联系管理员！:Err:S0x04';
									$params['sign']='forbidden';
									result_back(6,$params);
								}
								
							 
								// 检查当前用户最大发送量	
								$surplus =  $opt->Get_shop_msm($shop_id,$openid,$myinfo['m_level']);
									
								 // print_r($surplus);exit();	

								if (!((int)($surplus['sms_surplus'])>0))
								{
									$params['tips']='短信不足！';
									$params['sign']='cancel';
									result_back(6,$params);
								}

								if ($surplus['sms_status']==0)
								{
									$params['tips']='短信未开启';
									$params['sign']='cancel';
									result_back(6,$params);
								}

								
								//	 
								
								$wait_item=$opt->Get_sms_wait_item($openid);
								
								 
								
								if(!empty($wait_item))
								{
									$params['id']=$wait_item['id'];
									//先发短信，错误跳出
									$ali_callback = $opt ->send_sms_by_Ali($openid,$tel,$shop_id,$wait_item,$sms_type=3);
									if ($ali_callback->Message !='OK')
									{
										$params['tips']='阿里平台有错误，请检查！';
										$params['sign']='cancel';
										result_back(6,$params);
									}
									$opt ->Update_record_status($wait_item['id']);
									
									// echo $surplus['sms_id'];
									//-----------更新短信数量
									$opt->Uptdate_sms_surplus($surplus['sms_id'],$surplus['sms_used']+1,$surplus['sms_surplus']-1);
									// TODO:检测发送状态，如果错误则停止.发送短信减除
									
									// $opt->send_sms_by_data($openid,$wait_item,$shop_id,$wait_item,$sms_type=3);
									// $opt->send_sms($openid,$wait_item['recoder_tel'],$shop_id,$wait_item['recoder_code'],$sms_type=3);
								
									$opt->Update_stats_send_notice($wait_item['recoder_shop_id'],$notice_type=1);
									$remove['id']=$wait_item['id'];
									result_back(1,$remove);
								}
								else
								{
									result_back(0);
								}
							}
					}
					else
					{
						 result_back(0);
					}
				}

			if ($act=='load_list')
			{

				//此处需要增加权限设置，只能看自己店面的或者自己增加的列表
				
				$pindex = max(1, intval($_GPC['page']));
				$psize = 1000;
				
				if($member->is_shop_manage($openid))
				{
					$params = array(
						'recoder_get_status' => 0,
						'recoder_send_status' => 0,
						'uniacid' => $_W['uniacid'],
						'recoder_shop_id' => $member->Get_shop_id($openid),
					);  
					$condition=' WHERE recoder_get_status =:recoder_get_status and recoder_shop_id =:recoder_shop_id  and  recoder_send_status =:recoder_send_status and uniacid = :uniacid';
				}
				else
				{
					$params = array(
					'recoder_get_status' => 0,
					'recoder_send_status' => 0,
					'uniacid' => $_W['uniacid'],
					'recoder_add_openid' => $openid,
					);  
					$condition=' WHERE recoder_get_status =:recoder_get_status and recoder_add_openid =:recoder_add_openid and  recoder_send_status =:recoder_send_status and  uniacid = :uniacid';
				}
				$orderby=" order by id DESC";


				$wxmsn=0;
				$msn=0;

				$sql = "SELECT * from ".tablename($express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
				
				
				$list = pdo_fetchall($sql,$params);

				foreach ($list as $key => $list) {

					$bindstatus=empty($list['recoder_tel_exsit'])?'短信通知':'微信通知';
					$listcolor=empty($list['recoder_tel_exsit'])?' weui-cell_warn':'';

					$list['recoder_tel_exsit']==1?$wxmsn++:$msn++;

					$list_str .='<div class="weui-cell '.$listcolor.'"  id ="list'.$list['id'].'"  onclick="del('.$list['id'].')" ><div class="weui-cell__hd" style="width:70% ;text-align:center"><label for="date" class="weui-label">'.$list["recoder_tel"].'</label></div><div class="weui-cell__bd" style="width:30% ;text-align:center"><label for="date" class="weui-label">'.$bindstatus.'</label></div></div>';
				}

				$res['list_str']=$list_str;
				$res['wxmsn']=$wxmsn;
				$res['msn']=$msn;			
				$re=json_encode($res);
			    exit($re);
			}


			//新增记录
			if ($act=='addnew')
			{
					
					if ($myinfo['m_level']==4)
					{
					
						$where= ' where e_openid like :e_openid and uniacid = :uniacid';
						$params = array(
						'uniacid' => $_W['uniacid'],
						'e_openid' =>$openid,
						);   
						$m_item = pdo_fetch("SELECT * FROM ".tablename($express_employee). $where,$params);	
						
						if ($m_item['e_send_day']>=$m_item['e_send_day_max'] && $m_item['e_input_status']==1)
						{
							 result_back(2);
						}
						else
						{
							pdo_update($express_employee,array('e_send_day'=>$m_item['e_send_day']+1),array('e_id'=>$m_item['e_id']));
						}
						
					}
								
					
					
					$voicets=trim($_GPC['voicets']);
					$shelves=(int)($_GPC['shelves']);
					$goodno=(int)($_GPC['goodno']);
					$voicets_len=strlen($voicets);


					$barcode=trim($_GPC['barcode']);
					if ($_GPC['inputtype']==1)
					{
						$voicets_len=14;
					}

					if ($voicets_len==14)
						
					{
						$voicets=substr($voicets, 0, 11);


						//++++++++++++++++++++++++
										
						$bc_params = array(
							'recoder_barcode' => $barcode,
							'uniacid' => $_W['uniacid'],
							); 

						$bc_exsit= pdo_fetch("select id from ".tablename($express_recode)." where recoder_barcode =:recoder_barcode and uniacid =:uniacid order by id DESC limit 0,2000 ",$bc_params);

						
						if ($bc_exsit['id']>0)
						{
							$res['success']=0;
							result_back(0,$res);
						}
						
						if(preg_match("/^1[356789]{1}\d{9}$/",$voicets))
						{ 
							//根据号码取得opend,此openid为需发送
							$params = array(
							'm_tel' => $voicets,
							'uniacid' => $_W['uniacid'],
							); 
							$where=' WHERE m_tel = :m_tel and uniacid = :uniacid';		
							$item = pdo_fetch("SELECT *  FROM ".tablename($express_member). $where,$params);
							//根据号码取得opend,此openid为需发送
							$tel_exsit=empty($item)?0:1;
							
							$member_shop_id = $member->Get_shop_id($openid);
							
							//电话号码检测，更方便输入数据，2018.11.5
							if(1)
							{

								$item_a = pdo_fetch("SELECT count(*) as cnt  FROM ".tablename($express_tel_list). $where,$params);

								if ($item_a['cnt']>0) 
								{
									;
								} 
								else
								{
								$end_no=substr($voicets,-5);
								$tel_data = array(		 
								'uniacid' => $_W['uniacid'],
								'm_tel' => $voicets,
								'tl_tel_end_no' => $end_no,
								'tl_shop_in' => $member_shop_id,
								'tl_addtime' => time(),
								'tl_add_openid' => $openid,
								);
								$res_tel=pdo_insert($express_tel_list, $tel_data);

								} 

							}

							
							if (!empty($barcode))
								//快递公司不准确，手动设置，成熟之后采用些模块
								//$kd_com=  $kd_curl->Get_express_company($barcode,$member_shop_id);
							if (empty($kd_com))
								//$kd_com="未知";
							// for($i =0;$i <599;$i++)
							// {
								$data = array(
									'recoder_add_openid' =>$openid, 
									'recoder_shop_id' =>$member_shop_id,
									'recoder_create' => time(),
									'recoder_tel' =>$voicets,
									//'recoder_express_name' =>$kd_com,
									'recoder_express_name' =>trim($_GPC['express_name_v']),
									'recoder_shelves' =>$shelves,
									'recoder_goods_num' =>$goodno,
									'recoder_tel_exsit' =>$tel_exsit,
									'recoder_code' =>date("md",time()).rand(1000,9999),
									'recoder_status' =>empty($item)?1:$item['m_allow_notice'],
									'recoder_openid' =>$item['m_openid'], 
									'recoder_nickname' =>$item['m_nickname'], 
									'recoder_barcode' =>$barcode,
									'recoder_create_year' =>date("Y",time()),
									'recoder_create_month' =>date("m",time()),
									'recoder_create_day' =>date("d",time()),
									'recoder_create_hour' =>date("H",time()),
									'recoder_in_level' =>$myinfo['m_level'],
									
									
									'uniacid' => $_W['uniacid'],
								);		
								$result = pdo_insert($express_recode, $data);
								$tempid = pdo_insertid();
							// }
							//0微信，1短信
							
							$opt ->Stats_data_change($member->Get_shop_id($openid),1,!$tel_exsit);

							if ($result) 
			 				{	

								$res['shelves']=($shelves==0)?'0-':$shelves.'-';
								$res['goodno']=($goodno==0)?'0-':$goodno;
								$res['resultid']=$tempid;
			 					// $res['id']=$result['id'];
			 					$res['exsit']=empty($item)?0:1;
		 					    $res['success']=1;
								$res['voicets']=$voicets; 			
								$re=json_encode($res);
								exit($re); 
			 				}
			  					
						}


					}
					 
					 $res['voicets']=$voicets;			
					 $res['success']=0;			
				     $re=json_encode($res);
				     exit($re);
			}

			 

			
		}

		$express_list =pdo_fetchall("select ecode_id,ecode_name from ims_yiheng_express_ecode order by ecode_orderby ASC limit 0,14");

		foreach ($express_list as $key => $value) {
			$express_list_temp[$key]['title'] =$value['ecode_name'];
			$express_list_temp[$key]['value'] =$value['ecode_id'];
		}

		$express_list_str=json_encode($express_list_temp);


		if ($myinfo['m_level']==4)
		{
		
			$where= ' where e_openid like :e_openid and uniacid = :uniacid';
			$params = array(
			'uniacid' => $_W['uniacid'],
			'e_openid' =>$openid,
			);   
			$m_item = pdo_fetch("SELECT * FROM ".tablename($express_employee). $where,$params);	
			
		}


		include $this->template('notice_send');  
		
	}	
	 
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






















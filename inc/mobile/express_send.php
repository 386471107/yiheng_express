<?php 
global $_GPC, $_W;




$express_address_detail='yiheng_express_address_detail';

$uid=$_W['member']['uid'];

$params = array(
				// 'addr_uid' => $uid,
				// 'uniacid' => $_W['uniacid'],
				'addr_uid' => 17,
				'uniacid' => 3,
				);  
$where=' WHERE  addr_uid =:addr_uid and uniacid = :uniacid';
$orderby =" order by id DESC";		
$list = pdo_fetchall("SELECT *  FROM ".tablename($express_address_detail). $where.$orderby,$params);



if ($_W['isajax'])
		{

			$act=trim($_GPC['act']);



			if ($act =="add_addr")
			{
				$name=trim($_GPC['name']);
				$tel=trim($_GPC['tel']);
				$city=trim($_GPC['city']);
				$addr=trim($_GPC['addr']);
				$type=(int)($_GPC['type']);
				if (empty($name) || empty($tel) || empty($city) || empty($addr))
				{
					$res['success']=0;			
				    $re=json_encode($res);
				    exit($re);
				}
		
				$add_addr_params = array(
					'addr_uid' => $uid,
					'addr_tel' => $tel,
					'addr_addr' =>$city.$addr,
					'addr_name' => $name,
					'addr_use_time' => time(),
					'addr_type' =>$type,
					// 'addr_use_often' => 3,
					'uniacid' =>  $_W['uniacid'],
				);  

				$result = pdo_insert($express_address_detail, $add_addr_params);

				$res['id']=pdo_insertid();
				$res['tel']=$tel;
				$res['addr']=$city.$addr;
				$res['name']=$name;
				$res['type']=$type;
				
				$res['success']=1;			
			    $re=json_encode($res);
			     exit($re);
			}

			if ($act =="del_addr")
			{
				
				//注意权限 判断，2018.3.8 需更改其它页面
				$id=(int)$_GPC['id'];


				$result = pdo_delete($express_address_detail, array('id' => $id,'addr_uid' => $uid));
				
				$res['success']=1;			
				$re=json_encode($res);
				exit($re);

			}

		}



include $this->template('express_send');  
exit();

	// print_r($_SESSION);
	$title = '管理中心-发送端';

	include_once MODULE_ROOT.'/inc/func/nbyiheng.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	
	$express_member='yiheng_express_member';
	$express_recode='yiheng_express_recode';



	$openid=$_W['fans']['openid'];

	$NbyihengFunc = new Yiheng_express();
	
	$member = new yh_member();
	
	empty($openid)?$NbyihengFunc->To_login_wx():'';

	checkauth(); 

	$mitem = $member->getMember($openid);
	
	if ($mitem['m_is_manage_weixin']==1 || $mitem['m_is_manage_shop']==1)
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


			

			if ($act=='senddone')
			{

				 $udata = array(
						'recoder_send_status' => 0,

						); 
				 $result = pdo_update($express_recode, $udata);

				
			}


			if ($act=='sending')
			{

					$type=trim($_GPC['type']);

					if ($type=="weixin" || $type=="msm"  )
					{ 



							if ($type=="weixin")
							{


								$params = array(
										'recoder_tel_exsit' => 1,
										'uniacid' => $_W['uniacid'],
										'recoder_send_status' => 0,
										);  
										$where=' WHERE  recoder_tel_exsit =:recoder_tel_exsit and recoder_send_status =:recoder_send_status and uniacid = :uniacid';		
										$item = pdo_fetch("SELECT *  FROM ".tablename($express_recode). $where,$params);
									

									if(!empty($item))
									{
										 $res['success']=1;	
										 $res['id']=$item['id'];	
										 $udata = array(
												'recoder_send_status' => 1,
												'recoder_senttime' => time(),
												); 
										 $result = pdo_update($express_recode, $udata, array('id' => $item['id']));

										 $datainfo= array(
													'first' => '您的快递已存入一恒网络快件货架'."\n",
													'keyword1' => $item['recoder_code'],
													'keyword2' => rand(1111111,9999999),
													'keyword3' => rand(1111111,9999999),
													'keyword4' => '15168589418',
													'remark' => "\n".'请您凭取件码到一恒网络快件站人工取件，谢谢',
												); 
										 // $NbyihengFunc->sendmessage_input($openid,$datainfo);
										 $re=json_encode($res); 
									     exit($re);
									} 
									else
									{
										$res['success']=0;	
										 $re=json_encode($res);
									     exit($re);
									}

							}

							if ($type=="msm")
							{


								$params = array(
										'recoder_tel_exsit' => 0,
										'uniacid' => $_W['uniacid'],
										'recoder_send_status' => 0,
										);  
										$where=' WHERE  recoder_tel_exsit =:recoder_tel_exsit and recoder_send_status =:recoder_send_status and uniacid = :uniacid';		
										$item = pdo_fetch("SELECT *  FROM ".tablename($express_recode). $where,$params);

									if(!empty($item))
									{
										 $res['success']=1;	
										 $res['id']=$item['id'];
										 $udata = array(
												'recoder_send_status' => 1,
												'recoder_senttime' => time(),
												); 
										 $result = pdo_update($express_recode, $udata, array('id' => $item['id']));

										$NbyihengFunc->send_sms($item['recoder_tel'],$openid,1,$item['recoder_code'],$_W['uniacid'],$_W['clientip']);
										 $re=json_encode($res); 
									     exit($re);
									}
									else
									{
										$res['success']=0;	
										 $re=json_encode($res);
									     exit($re);
									}

							}


							
					}
					else
					{
						 $res['success']=0;	
						 $re=json_encode($res);
					     exit($re);
					}				
					

				}


			



			if ($act=='load_list')
			{
				
				$pindex = max(1, intval($_GPC['page']));
				$psize = 100;
				$condition =" where uniacid =:uniacid  and recoder_send_status =:recoder_send_status and recoder_status =:recoder_status";
				$orderby=" order by id DESC";
				$params = array( 
							'uniacid' => $_W['uniacid'],
							'recoder_send_status' => 0,
							'recoder_status' =>1,
					);
				
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







			if ($act=='addnew')
			{

					$voicets=trim($_GPC['voicets']);
					$voicets_len=strlen($voicets);

					if ($_GPC['inputtype']==1)
					{
						$voicets_len=14;
					}

					if ($voicets_len==14)
						// if (1)
					{
						$voicets=substr($voicets, 0, 11);

						if(preg_match("/^1[34578]{1}\d{9}$/",$voicets))
						{ 
							$params = array(
							'm_tel' => $voicets,
							'uniacid' => $_W['uniacid'],
							); 
				
							$where=' WHERE m_tel = :m_tel and uniacid = :uniacid';		
							$item = pdo_fetch("SELECT *  FROM ".tablename($express_member). $where,$params);
							
		 					
							$data = array(
								'recoder_uid' => $item['m_uid'],
								'recoder_create' => time(),
								'recoder_tel' =>$voicets,
								'recoder_tel_exsit' =>empty($item)?0:1,
								'recoder_code' =>date("Ymd",time()).rand(1000,9999),
								'recoder_status' =>empty($item)?1:$item['m_allow_notice'],
								'recoder_openid' =>$item['m_openid'], 
								'uniacid' => $_W['uniacid'],
							);
							 $result = pdo_insert($express_recode, $data);
							
							if ($result) 
			 				{	
			 					$res['resultid']=pdo_insertid();
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




		include $this->template('express_sendmsg');  
		exit();
	}	
	else
	{
		//TODO :jump to limit page;
	}





	exit();
	



  ?>
	






















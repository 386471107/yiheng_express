<?php 




global $_GPC, $_W;


include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_kd_curl.php';
	
	$express_member='yiheng_express_member';
	$express_recode='yiheng_express_recode';

	// $express_tpl='yiheng_express_tpl';
	// $express_sms_log='yiheng_express_sms_log';
	// $express_sms_setting='yiheng_express_sms_setting';
	
	$member = new yh_member();
	$opt = new yh_opt();
	$kd_curl = new yh_kd_curl();

	$openid="opr2TjvOGvaMGe_pyYWsaUCqEzIk";
					$barcode=trim($_GPC['barcode']);

					$voicets=trim($_GPC['voicets']);
					$shelves=(int)($_GPC['shelves']);
					$goodno=(int)($_GPC['goodno']);
					$voicets_len=strlen($voicets);
					
					if ($_GPC['inputtype']==1)
					{
						$voicets_len=14;
					}

					if ($voicets_len==11)
						
					{

						$voicets=substr($voicets, 0, 11);
						if(preg_match("/^1[34578]{1}\d{9}$/",$voicets))
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
							if (!empty($barcode))
								// $kd_com=  $kd_curl->Get_express_company($barcode,$member_shop_id);
							if (empty($kd_com))
								$kd_com="未知";
							// for($i =0;$i <599;$i++)
							// {
											$data = array(
												'recoder_add_openid' =>$openid, 
												'recoder_shop_id' =>$member_shop_id,
												'recoder_create' => time(),
												'recoder_tel' =>$voicets,
												// 'recoder_tel' =>'1373887'.rand(1000,9999),
												'recoder_express_name' =>$kd_com,
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
												'uniacid' => $_W['uniacid'],
											);
											
											$result = pdo_insert($express_recode, $data);
							// }
							//0微信，1短信
							
							$opt ->Stats_data_change($member->Get_shop_id($openid),1,!$tel_exsit);

							if ($result) 
			 				{	

								$res['shelves']=($shelves==0)?'无-':$shelves.'-';
								$res['goodno']=($goodno==0)?'无':$goodno;
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



exit();
 include $this->template('test');  
// include $this->template('test');  

 ?>
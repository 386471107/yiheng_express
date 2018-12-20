<?php 

global $_GPC, $_W;


if ($_SESSION['m_is_manage_weixin'] !=1)
	{
		$forward = $this->createMobileUrl('error');
        header('Location: ' . $forward);
	}

$title = '管理中心-扫描入库';

$express_member='yiheng_express_member';
$express_recode='yiheng_express_recode';
$express_reg='yiheng_express_reg';
$express_wait_pickup='yiheng_express_wait_pickup';
$express_tpl='yiheng_express_tpl';
$uniacid=$_W['uniacid'];


include_once MODULE_ROOT.'/inc/func/nbyiheng.func.php';
$NbyihengFunc = new Yiheng_express();

	if ($_W['isajax']==1)
		{ 

			 
			
			if ($_GPC['act']=='del')
			{

				$delid=(int)$_GPC['id'];
				$result = pdo_delete($express_recode, array('id' => $delid));
				$res['success']=1;			
				$re=json_encode($res);
				exit($re);

			}
			
			if ($_GPC['act']=='scan')
			{ 
				
				$serialNumber=$_GPC['serialNumber'];				
				$shelves=(int)$_GPC['shelves'];
				$goodno=(int)$_GPC['goodno'];
				if (strlen($serialNumber)>9 && strlen($serialNumber)<16 && is_numeric($serialNumber))
					
				{
					//搜索记录中是否存在等待收件列表
					

					$recoder_code=date("md",time()).rand(100000,999999);

					$where=' WHERE recoder_express_id =:recoder_express_id and uniacid = :uniacid';		
							$params = array(
							'recoder_express_id' => $serialNumber,
							'uniacid' => $_W['uniacid'],
							); 
							
							$item = pdo_fetch("select * FROM ".tablename($express_recode). $where,$params);

							
							if ($item)
							{
								$res['success']=3;
								$re=json_encode($res);
								exit($re); 

							}
							
							//搜索记录中是否存在等待收件列表
						
							//搜索等待收件是否存在单号
							 $exist_where=' WHERE wait_express_no =:wait_express_no and wait_status =:wait_status and wait_sendstatus =:wait_sendstatus and uniacid = :uniacid';		
							$exist_params = array(
							'wait_express_no' => $serialNumber,
							'uniacid' => $_W['uniacid'],
							'wait_status' => 1,
							'wait_sendstatus' => 0,

							); 


							//可能有多人输入同一快递号
							$exist_list = pdo_fetchall("select * FROM ".tablename($express_wait_pickup). $exist_where,$exist_params);


							
							if (!empty($exist_list))
							{
								
								//如果有记录存在，则发送消息
								$tplwhere=" where uniacid =:uniacid and tpl_status =:tpl_status";	
											$tplparams = array(
											'uniacid' => $_W['uniacid'],
											'tpl_status' =>1,
											);  
								$tplinfo = pdo_fetch("SELECT *  FROM ".tablename($express_tpl). $tplwhere,$tplparams);

								if (!empty($tplinfo))
								{
									$datainfo= array(
									'first' => $tplinfo['tpl_title']."\n",
									'keyword1' => $tplinfo['tpl_kw1'],
									'keyword2' => $recoder_code,
									'remark' => "\n".$tplinfo['tpl_remark']."\n"."货架号为".$shelves.'-'.$goodno,
									); 
									$tplid=$tplinfo['tpl_id'];


									foreach ($exist_list as $key => $exist_lista) {
									 		$NbyihengFunc->sendmessage_inputa($_W['openid'],$tplid,$datainfo,'wait_pickup');
										
											$wait_data = array(
											'wait_sendtime' => time(),	
											'wait_sendstatus' => 1,	
												
											'wait_exsit' => 1,	
											);
											$result = pdo_update($express_wait_pickup, $wait_data, array('wait_openid' => $exist_lista['wait_openid'],'uniacid' => $exist_lista['uniacid']));
											
								
									 } 

								
								
								}
								//更新等待列表中信息

							}

							//搜索等待收件是否存在单号

						

					$data = array(
								'recoder_express_id' =>$serialNumber,
								'recoder_create' => time(),								
								'recoder_shelves' =>$shelves,
								'recoder_goods_num' =>$goodno,
								'recoder_tel_exsit' =>0,
								'recoder_code' =>$recoder_code,
								'recoder_status' =>0,
								'recoder_by_express_id' =>1,									
								'uniacid' => $_W['uniacid'],
							);
					$result = pdo_insert($express_recode, $data);


					$product['resultid']=pdo_insertid();
					$product['success']=($result==1)?1:1;

					$re=json_encode($product);

					exit($re);
						


				}
				else 
				{

					# code...
				}
							

				
			}
				
	
			
		}
		

	
	
	include $this->template('express_scan');  
	exit(); 
	
	 ?>
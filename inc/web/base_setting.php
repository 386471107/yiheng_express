<?php 



	global $_GPC, $_W;

	// include MODULE_ROOT.'/inc/mobile/__init.php';
	checklogin();
	$title = '基本设置';
	$op = in_array($_GPC['op'], array('sms','noticetpl','renew'))?$_GPC['op']:'sms';
	
	$express_sms_setting='yiheng_express_sms_setting';
	$express_tpl='yiheng_express_tpl';
	$express_area='yiheng_express_area';
	$express_sms_setting='yiheng_express_sms_setting';
	$express_tpl_example='yiheng_express_tpl_example';



	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';

	$wopt = new yh_web_option();
 	
	// if ($op == 'renew')
	// {
	// 	$tpl_all = $wopt ->Get_tpl_all();
	// 	pdo_query("delete from ".tablename($express_tpl_example). " where uniacid = ".$_W['uniacid']);
	// 	foreach ($tpl_all as $key => $value) {
	// 	if (!empty($value['example']))
	// 	{
	// 		$example= $value['example']."\n";
	// 		$matches=array();
	// 		preg_match_all("/.*?\n/",$example,$matches);
	// 		$item['tpl_id']=$value['template_id'];
	// 		$item['tpl_title']=$matches[0][0];
	// 		$item['tpl_remark']=$matches[0][sizeof($matches[0])-1];
	// 		$item['tpl_size']=sizeof($matches[0])-2;
	// 		$item['uniacid']=$_W['uniacid'];
	// 		$item['tpl_alert_title']=$value['title'];
	// 		$item['tpl_primary_industry']=$value['primary_industry'];
	// 		$item['tpl_deputy_industry']=$value['deputy_industry'];

	// 		switch (sizeof($matches[0])) {
	// 		case 3:
	// 			$item['tpl_kw1']=$matches[0][1];
	// 			break;
	// 		case 4:
	// 			$item['tpl_kw1']=$matches[0][1];
	// 			$item['tpl_kw2']=$matches[0][2];
	// 			break;
	// 		case 5:
	// 			$item['tpl_kw1']=$matches[0][1];
	// 			$item['tpl_kw2']=$matches[0][2];
	// 			$item['tpl_kw3']=$matches[0][3];
	// 			break;
	// 		case 6:
	// 			$item['tpl_kw1']=$matches[0][1];
	// 			$item['tpl_kw2']=$matches[0][2];
	// 			$item['tpl_kw3']=$matches[0][3];
	// 			$item['tpl_kw4']=$matches[0][4];
	// 			break;
	// 		case 7:
	// 			$item['tpl_kw1']=$matches[0][1];
	// 			$item['tpl_kw2']=$matches[0][2];
	// 			$item['tpl_kw3']=$matches[0][3];
	// 			$item['tpl_kw4']=$matches[0][4];
	// 			$item['tpl_kw5']=$matches[0][5];
	// 			break;
	// 		default:
	// 			# code...
	// 			break;
	// 		}
	// 		 $result=pdo_insert($express_tpl_example, $item);
			
			 
	// 	 }
	// 	}
	// 	if ($result) {
	// 			message('示例模版加载成功', $this->createWebUrl('base_setting'), 'success');
	// 			} else {
	// 			message('示例模版加载失败，请重试');
	// 			}
	// }

 	if (!$_W['isajax'])
	{			
	$tpl_all = $wopt ->Get_tpl_all();
	pdo_query("delete from ".tablename($express_tpl_example). " where uniacid = ".$_W['uniacid']);
	foreach ($tpl_all as $key => $value) {
		if (!empty($value['example']))
		{
			$example= $value['example']."\n";
			$matches=array();
			preg_match_all("/.*?\n/",$example,$matches);
			$item['tpl_id']=$value['template_id'];
			$item['tpl_title']=$matches[0][0];
			$item['tpl_remark']=$matches[0][sizeof($matches[0])-1];
			$item['tpl_size']=sizeof($matches[0])-2;
			$item['uniacid']=$_W['uniacid'];
			$item['tpl_alert_title']=$value['title'];
			$item['tpl_primary_industry']=$value['primary_industry'];
			$item['tpl_deputy_industry']=$value['deputy_industry'];

			switch (sizeof($matches[0])) {
			case 3:
				$item['tpl_kw1']=$matches[0][1];
				break;
			case 4:
				$item['tpl_kw1']=$matches[0][1];
				$item['tpl_kw2']=$matches[0][2];
				break;
			case 5:
				$item['tpl_kw1']=$matches[0][1];
				$item['tpl_kw2']=$matches[0][2];
				$item['tpl_kw3']=$matches[0][3];
				break;
			case 6:
				$item['tpl_kw1']=$matches[0][1];
				$item['tpl_kw2']=$matches[0][2];
				$item['tpl_kw3']=$matches[0][3];
				$item['tpl_kw4']=$matches[0][4];
				break;
			case 7:
				$item['tpl_kw1']=$matches[0][1];
				$item['tpl_kw2']=$matches[0][2];
				$item['tpl_kw3']=$matches[0][3];
				$item['tpl_kw4']=$matches[0][4];
				$item['tpl_kw5']=$matches[0][5];
				break;
			default:
				# code...
				break;
			}
			 $result=pdo_insert($express_tpl_example, $item);
		 }
		}
		}
		if ($_W['isajax'])
		{
			$act=trim($_GPC['act']);
			$area_code=(int)($_GPC['area_code']);
			$tpl_list_id=(int)($_GPC['tpl_list_id']);
			if ($act=='change' && $area_code )
			{

				$params = array(
								'sms_area_id' => $area_code,
								);
				$where=' WHERE sms_area_id = :sms_area_id';	
				$orderby=' order by sms_type asc';
				$limit =" limit 1";
				$sms_info = pdo_fetchall("SELECT *  FROM ".tablename($express_sms_setting). $where.$orderby,$params);
				if (!empty($sms_info))
				{
				result_back(1,$sms_info);
				}
				else
				{
				result_back(0);
				} 

			}

			if ($act=='tpl_change' && $area_code )
			{

			
				$params = array(
								'tpl_shop_id' => $area_code,
								);
				$where=' WHERE tpl_shop_id = :tpl_shop_id';	
				$orderby=' ';
				$limit =" limit 1";
				$tpl_info = pdo_fetch("SELECT *  FROM ".tablename($express_tpl). $where.$orderby,$params);
			
				if (!empty($tpl_info))
				{
				result_back(1,$tpl_info);
				}
				else
				{
				result_back(0);
				}

			}


				if ($act=='change_tpl' && $tpl_list_id )
				{

				
					$params = array(
									'id' => $tpl_list_id,
									'uniacid' => $_W['uniacid'],
									);
					$where=' WHERE id = :id and uniacid =:uniacid';	
					$orderby=' ';
					$limit =" limit 1";
					$tpl_info = pdo_fetch("SELECT *  FROM ".tablename($express_tpl_example). $where.$orderby,$params);
					
					if (!empty($tpl_info))
					{
					result_back(1,$tpl_info);
					}
					else
					{
					result_back(0);
					}

				}




			
		}


		if(checksubmit('sms_submit'))
		{
			$area_code=(int)($_GPC['area_code']);
			if($area_code==0)
			{
				message('店铺未选择');
			}

			for ($i = 1;$i <4;$i ++)
			{
				$data = array(
							'sms_key' => $_GPC['sms_key'],
							'sms_message_id' =>  $_GPC['sms_id'][$i],
							'sms_message' =>  $_GPC['sms_content'][$i],

							);
				if ($i==3)
				{ 
					// $data['sms_do']=1;
					$data['sms_day_max']=$_GPC['sms_day_max'][$i];

				}

			$where= " where sms_type=$i and sms_area_id = $area_code and uniacid=".$_W['uniacid'];	
			$exsit = pdo_fetch("SELECT * FROM ".tablename($express_sms_setting). $where.$orderby);

				if (empty($exsit))
				{

					$data['sms_type']=$i;
					$data['uniacid']=$_W['uniacid'];
					$data['sms_area_id']=$area_code;
					$result = pdo_insert($express_sms_setting, $data);
				}
				else
				{
					// $data['sms_do']=1;

					$result = pdo_update($express_sms_setting, $data, array('sms_area_id' => $area_code,'sms_type' => $i,'uniacid' =>$_W['uniacid']));	
				}
			
			}

			if ($result) 
			message('短信设置成功！', $this->createWebUrl('base_setting'), 'success');
			message('短信设置不成功！', $this->createWebUrl('base_setting'), 'error');
		}
		if(checksubmit('noticetpl'))
		{
			$area_code=(int)($_GPC['area_code']);
			$data = array(
					'tpl_id' => $_GPC['tpl_id'],
					'tpl_title' =>  $_GPC['tpl_title'],
					'tpl_kw1' =>  $_GPC['tpl_kw1'],
					'tpl_kw2' =>  $_GPC['tpl_kw2'],
					'tpl_kw3' =>  $_GPC['tpl_kw3'],
					'tpl_kw4' =>  $_GPC['tpl_kw4'],
					'tpl_kw5' =>  $_GPC['tpl_kw5'],
					'tpl_remark' =>  $_GPC['tpl_remark'],
					'tpl_status' =>  1,
					'tpl_size' =>  $_GPC['tpl_size'],
					'tpl_shop_id' =>  $area_code,
					); 
 
			if($area_code==0)
			{
				message('店铺未选择');
			}

			$where= " where uniacid=".$_W['uniacid']." and tpl_shop_id =".$area_code;	
			$exsit = pdo_fetch("SELECT * FROM ".tablename($express_tpl). $where.$orderby);
			
			if (empty($exsit))
			{
				$data['uniacid']=$_W['uniacid'];
				$result = pdo_insert($express_tpl, $data);
				message('模版消息新增成功！', $this->createWebUrl('base_setting'), 'success');
			}
			else
			{
				$result = pdo_update($express_tpl, $data, array('uniacid' =>$_W['uniacid'],'tpl_shop_id' =>$area_code));
				message('模版消息更新成功！', $this->createWebUrl('base_setting'), 'success');
			}
			
			
		}
		
		
	$params = array(
					'uniacid' => $_W['uniacid'],
					);
		
	$where=' where uniacid = :uniacid';		
	$list = pdo_fetchall("SELECT * FROM ".tablename($express_sms_setting). $where.$orderby,$params);
	
	foreach ($list as $lista)

	{
		 $dislist[$lista["sms_type"]]['sms_message_id']=$lista['sms_message_id'];
		 $dislist[$lista["sms_type"]]['sms_message']=$lista['sms_message'];
		  $dislist[$lista["sms_type"]]['sms_d_max']=$lista['sms_day_max'];
		  // $dislist[$lista["sms_type"]]['sms_do']=$lista['sms_do'];
	}


		
		
	$params = array(
					'uniacid' => $_W['uniacid'],
					);
		
	$where=' where uniacid = :uniacid';	
	$itemtpl = pdo_fetch("SELECT * FROM ".tablename($express_tpl). $where.$orderby,$params);
		


	$where=' WHERE uniacid = :uniacid ';	
	$orderby='';	
    $params = array(
	'uniacid' => $_W['uniacid'],								
	);
    $sql = "SELECT * from ".tablename($express_area).$where;
	$area_list = pdo_fetchall($sql,$params);
		
	$sql = "SELECT * from ".tablename($express_tpl_example).$where;
	$tpl_list = pdo_fetchall($sql,$params);
		
	
		
	 include $this->template('web/base_setting');  
	
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
<?php 

	global $_GPC, $_W;



	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();

	$express_tpl='yiheng_express_tpl';
	$express_tpl_example='yiheng_express_tpl_example';

	$def_shop_id = $wopt->Get_web_def_shop();


	$act = $_GPC['act'];
	
	
	
	
	 if ($act=='copy_tpl')
	 {
		 
		 $paramsa = array(
				'uniacid' => $_W['uniacid'],
				'tpl_shop_id' => $def_shop_id,
			);
			$conditiona =" where uniacid =:uniacid and tpl_shop_id =:tpl_shop_id";
			$sql = "SELECT count(*) as cnt  from ".tablename($express_tpl).$conditiona;
			$exsit = pdo_fetch($sql,$paramsa);
			
			
			if ($exsit['cnt']==0)
			{
				
			
				$params = array(
				'uniacid' => $_W['uniacid'],
				);
				$condition =" where uniacid =:uniacid and tpl_type in(1,2,3)";
				$limit =' limit 0,3';
				$select ="*";
				$sql = "SELECT $select from ".tablename($express_tpl).$condition.$limit;
				$wx_list = pdo_fetchall($sql,$params);
				
				
				foreach($wx_list as $wx)
				{
					unset($wx['id']);
					$wx['tpl_shop_id']=$def_shop_id;
					pdo_insert($express_tpl,$wx);
					break; 
					//未有2，3类，先跳出
				}
				
			}
		
	 }
	 
	 
	
	
	if ($act == 'renew')
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
		if ($result) {
				message('示例模版加载成功', $this->createWebUrl('pc_weixin_tpl'), 'success');
				} else {
				message('示例模版加载失败，请重试');
				}
	}

	if(checksubmit('tpl_save'))
		{
			$tpl_id = trim($_GPC['tpl_id']);
			$example_data= $wopt->Get_tpl_example_by_tplid($tpl_id);
			if (!empty($example_data))
				{
					$kw1_data=explode("：",$example_data['tpl_kw1']);
					$kw2_data=explode("：",$example_data['tpl_kw2']);
					$kw3_data=explode("：",$example_data['tpl_kw3']);
					$kw4_data=explode("：",$example_data['tpl_kw4']);
					$kw5_data=explode("：",$example_data['tpl_kw5']);
					$data=array(
						'tpl_shop_id'=>$def_shop_id,
						'tpl_id'=>$tpl_id,
						'tpl_alert_title'=>$example_data['tpl_alert_title'],
						'tpl_title'=>trim($_GPC['tpl_title']),
						'tpl_kw1_pre'=>$kw1_data[0],
						'tpl_kw2_pre'=>$kw2_data[0],
						'tpl_kw3_pre'=>$kw3_data[0],
						'tpl_kw4_pre'=>$kw4_data[0],
						'tpl_kw5_pre'=>$kw5_data[0],
						'tpl_kw1'=>trim($_GPC['tpl_kw1']),
						'tpl_kw2'=>trim($_GPC['tpl_kw2']),
						'tpl_kw3'=>trim($_GPC['tpl_kw3']),
						'tpl_kw4'=>trim($_GPC['tpl_kw4']),
						'tpl_kw5'=>trim($_GPC['tpl_kw5']),
						'tpl_remark'=>trim($_GPC['tpl_remark']),
						'tpl_status'=>1,
						'tpl_size'=>$example_data['tpl_size'],
						'uniacid'=>$_W['uniacid'],
					);

					$resultA = pdo_update($express_tpl, $data, array('tpl_shop_id'=>$def_shop_id));
					if (0)
					$resultA = pdo_insert($express_tpl, $data);
					if ($resultA ) {
					message('修改模版消息成功', $this->createWebUrl('pc_weixin_tpl'), 'success');
					} else {
					message('修改模版消息失败，请重试');
					}
					exit();

				}
        	}
        	






	
	$tpl_example_all=$wopt-> Get_tpl_example_all();

	$my_tpl=$wopt-> Get_my_weixin_tpl($def_shop_id);
	

	if (intval($_GPC['set_def']))
	{
		$item = $wopt->Get_tpl_example_by_id(intval($_GPC['set_def']));
	}

	if (intval($_GPC['modify_def']))
	{
		$item = $wopt-> Get_my_weixin_tpl($def_shop_id);
	}


	
	include $this->template('pc/pc_weixin_tpl');  
	
	
	
?>
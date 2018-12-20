<?php 

	global $_GPC, $_W;
	
	$title = '默认网点选择';
  

	$openid=$_W['openid']; 

	if ($_SESSION['level'] !=1 && $_SESSION['level'] !=2 && $_SESSION['level'] !=3 || empty($openid))
	{
		$forward = $this->createMobileUrl('home');
	    header('Location: ' . $forward);
	}

	

	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';

	
	$express_member="yiheng_express_member";
	$express_level_recode="yiheng_express_level_recode";

	
	$member = new yh_member();
	$opt  = new yh_opt();
	if (empty($openid)) die();
$m_item=$member->Get_member($openid);
	//判断openid 是否存在，不存在则die();
	if ($_W['isajax'])
		{
			$id= $_GPC['id'];

			//level 记录表中，多店铺，多员工时权限记录
			$pramas=array(
			'uniacid' => $_W['uniacid'], 
			'lv_openid' => $openid,
			'lv_shop_id' => $id,
			); 
			$where  =" where lv_openid like :lv_openid and lv_shop_id =:lv_shop_id and uniacid=:uniacid";
			$item = pdo_fetch('select *  from ' . tablename($express_level_recode) . $where, $pramas);

			if(empty($item))
			{
				$data=array(
				'm_defaut_area' => $id,
				'm_level' =>0,
				);
				
			}
			else
			{
				$data=array(
				'm_defaut_area' => $id,
				'm_level' =>$item['lv_m_level'],
				);
			
			}


			$result = pdo_update($express_member, $data, array('id' => $m_item['id']));

			if ($result)
			 {
			 	if(empty($item))
					{
						$lv_data= array(
							'lv_shop_id' => $id,
							'lv_openid' =>$openid,
							'lv_m_level' => 0,
							'uniacid' => $_W['uniacid'],
								);			
						$lv_result = pdo_insert($express_level_recode, $lv_data);
						
					}
					else
					{
						$lv_data= array(
								'lv_m_level' => $m_item['m_level'],
								);			
						$lv_result = pdo_update($express_level_recode, $lv_data, array('lv_id' => $item['lv_id']));

					}	
	 			$params['tips']='更改成功！';
				$params['sign']='success';
				result_back(1,$params);
			 }
			 else
			 {
			 	$params['tips']='无需更改';
				$params['sign']='cancel';
				result_back(1,$params);
			 }



						

			

			if ($set_res)
			{	
				result_back(1,$res);	
			}
		}


	$m_item=$member->Get_member($openid);
	$shop_list = $member->Get_shop_list($openid);
 

	include $this->template('area_default');  
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
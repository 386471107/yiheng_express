<?php 

global $_GPC, $_W;
	$title = '我的店员';
 	include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	$express_member='yiheng_express_member';
	$express_recode='yiheng_express_recode';
	$tem=tablename($express_member);
	$ter=tablename($express_recode);
	$member = new yh_member();
	$opt = new yh_opt();

	$openid=$_W['openid'];
	$member->judge_db_member($openid);
	$member->Update_member_status($openid,$follow=$mc_info['subscribe'],$page="home");
	if (empty($openid)) die();



	$act=$_GPC['act'];
	$id=$_GPC['id'];
	$is_flag=$_GPC['is_flag'];

	
	if ($_W['isajax'])
		{
			$u_shop_id=$member->is_shop_manage($openid);
			if($act=='change_stats')
			{
				$change = $is_flag==0?3:0;
				$res = pdo_update($express_member,array('m_level' =>$change),array('uniacid' => $_W['uniacid'],'m_defaut_area' => $u_shop_id,'id' => $id));
				if ($res)
				{
					 result_back($change);
				}
 				result_back('cancel');
			}
		}


	// $opt->del_apply_timeout($openid);	
	$my_staff_list = ($opt->Get_my_shop_staff_list($openid,$page_index=1,$psize=500));
	
 	include $this->template('my_staff_list');  

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

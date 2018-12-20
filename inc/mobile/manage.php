<?php 

global $_GPC, $_W;


	include_once MODULE_ROOT.'/inc/mobile/common.php';
	
	$title = '权限管理';
	
	//走权限管理
	$page="manage";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理
	
	
	$act=$_GPC['act'];
	$id=$_GPC['id'];
	$is_flag=$_GPC['is_flag'];

	 
	 
	 
	 
	if ($_W['isajax'])
		{
			$u_shop_id=$member->is_shop_manage($openid);
			if($act=='change_stats' && $u_shop_id)
			{
				$change = $is_flag==3?0:3;
				$res = pdo_update($express_member,array('m_level' =>$change),array('uniacid' => $_W['uniacid'],'m_defaut_area' => $u_shop_id,'id' => $id));
				if ($res)
				{
					 result_back($change);
				}
 				result_back('cancel');
			}
		}

	$mlist = $opt->del_apply_timeout($openid);	
	$mlist = ($opt->Get_apply_list($openid,$page_index=1,$psize=500));


 include $this->template('manage');  
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

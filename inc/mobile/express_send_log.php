
<?php 

	global $_GPC, $_W;
	
	include_once MODULE_ROOT.'/inc/mobile/common.php';
	$title = '发送日志';
	
	//走权限管理
	$page="express_send_log";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理


	$list= $opt->Get_send_list_all($openid,$has_shop,$send=1,$page_index=1,$psize=200);
	
	$pager=$list['pager'];
	$list_log=$list['lst'];


	
	include $this->template('express_send_log');  
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































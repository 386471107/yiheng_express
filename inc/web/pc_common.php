<?php 

	global $_GPC, $_W;

	$act=$_GPC['act'];

	$express_web_user="yiheng_express_web_user";
	if ($_W['isajax'])
		{
			if($act=='c_shop')
				{

					
					$area_code =trim($_GPC['area_code']);
					empty($area_code)?result_back(0):'';

					pdo_update($express_web_user,array('web_default_shop' =>0),array('uniacid' =>$_W['uniacid']));
					pdo_update($express_web_user,array('web_default_shop' =>1),array('web_shop_id'=>$area_code,'uniacid' =>$_W['uniacid']));
					result_back(1);
			
				}
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
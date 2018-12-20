<?php 

	global $_GPC, $_W;
	
	$setopenid=$_GPC['setopenid']; 

	$express_member='yiheng_express_member';
	$udate=array(
	'm_sence_list' =>'["yh_1001","yh_1002"]',
	'm_area_list' =>'["1001",1002]',
	'm_level' =>'2',
	'm_defaut_area' =>'1001',
	);
	$result = pdo_update($express_member, $udate, array('m_openid' => $setopenid));
	if ($result)
	{
		message("设置成功！",'success');
	}
 ?>